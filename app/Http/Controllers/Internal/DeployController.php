<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class DeployController extends Controller
{
    public function run(Request $request): JsonResponse
    {
        $expected = (string) config('deploy.token', '');
        $provided = (string) $request->bearerToken();

        if ($expected === '' || !hash_equals($expected, $provided)) {
            return response()->json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        if ($request->boolean('seed')) {
            return $this->runSeed($request);
        }

        $rollback = $request->boolean('rollback');
        $directory = (string) config('deploy.directory');
        $appPath = (string) config('deploy.app_path');
        $publicPath = (string) config('deploy.public_path');
        $appArchive = $directory . '/' . ($rollback ? 'app-prev.zip' : 'app.zip');
        $publicArchive = $directory . '/' . ($rollback ? 'public-prev.zip' : 'public.zip');

        if (!is_file($appArchive) || !is_file($publicArchive)) {
            return response()->json(['ok' => false, 'error' => 'Archives missing'], 422);
        }

        if (!class_exists(ZipArchive::class)) {
            return response()->json(['ok' => false, 'error' => 'Zip support missing'], 422);
        }

        $steps = [];

        try {
            Artisan::call('down', ['--render' => 'errors::503']);
        } catch (\Throwable $throwable) {
            $steps[] = ['name' => 'down', 'ok' => false, 'detail' => substr($throwable->getMessage(), 0, 500)];
        }

        $extractedApp = $this->extractArchive($appArchive, $appPath);
        $steps[] = ['name' => 'extract-app', 'ok' => $extractedApp['ok'], 'detail' => $extractedApp['detail']];

        $extractedPublic = ['ok' => false, 'detail' => 'Skipped'];
        if ($extractedApp['ok']) {
            $extractedPublic = $this->extractArchive($publicArchive, $publicPath);
        }
        $steps[] = ['name' => 'extract-public', 'ok' => $extractedPublic['ok'], 'detail' => $extractedPublic['detail']];

        if ($extractedApp['ok'] && $extractedPublic['ok'] && !$rollback) {
            try {
                $backupApp = $directory . '/app-prev.zip';
                $backupPublic = $directory . '/public-prev.zip';
                if (is_file($appArchive) && !is_file($backupApp)) {
                    copy($appArchive, $backupApp);
                }
                if (is_file($publicArchive) && !is_file($backupPublic)) {
                    copy($publicArchive, $backupPublic);
                }
                $steps[] = ['name' => 'backup', 'ok' => true, 'detail' => 'Previous archives retained'];
            } catch (\Throwable $throwable) {
                $steps[] = ['name' => 'backup', 'ok' => false, 'detail' => substr($throwable->getMessage(), 0, 500)];
            }
        }

        $migrated = ['ok' => false, 'detail' => 'Skipped'];
        if ($extractedApp['ok'] && $extractedPublic['ok']) {
            try {
                Artisan::call('migrate', ['--force' => true]);
                $migrated = ['ok' => true, 'detail' => substr((string) Artisan::output(), 0, 2000)];
            } catch (\Throwable $throwable) {
                $migrated = ['ok' => false, 'detail' => substr($throwable->getMessage(), 0, 2000)];
            }
        }
        $steps[] = ['name' => 'migrate', 'ok' => $migrated['ok'], 'detail' => $migrated['detail']];

        $link = ['ok' => false, 'detail' => 'Skipped'];
        if ($migrated['ok']) {
            $link = $this->ensureStorageLink();
        }
        $steps[] = ['name' => 'storage-link', 'ok' => $link['ok'], 'detail' => $link['detail']];

        if ($migrated['ok']) {
            foreach (['config:clear', 'config:cache', 'route:cache', 'view:cache'] as $command) {
                try {
                    Artisan::call(str_contains($command, ':') ? explode(':', $command)[0] . ':' . explode(':', $command)[1] : $command);
                    $steps[] = ['name' => $command, 'ok' => true, 'detail' => substr((string) Artisan::output(), 0, 500)];
                } catch (\Throwable $throwable) {
                    $steps[] = ['name' => $command, 'ok' => false, 'detail' => substr($throwable->getMessage(), 0, 500)];
                }
            }
        }

        try {
            Artisan::call('up');
        } catch (\Throwable $throwable) {
            $steps[] = ['name' => 'up', 'ok' => false, 'detail' => substr($throwable->getMessage(), 0, 500)];
        }

        $succeeded = collect($steps)->every(fn ($step) => $step['ok'] === true);

        Log::info('deploy', [
            'rollback' => $rollback,
            'ip' => $request->ip(),
            'succeeded' => $succeeded,
            'steps' => collect($steps)->map(fn ($step) => $step['name'] . ':' . ($step['ok'] ? 'ok' : 'fail'))->values()->all(),
        ]);

        return response()->json(['ok' => $succeeded, 'steps' => $steps], $succeeded ? 200 : 500);
    }

    private function runSeed(Request $request): JsonResponse
    {
        try {
            $existing = DB::table('users')->count();
        } catch (\Throwable $throwable) {
            return response()->json(['ok' => false, 'error' => 'Seed check failed'], 500);
        }

        if ($existing > 0) {
            return response()->json(['ok' => false, 'error' => 'Already seeded'], 422);
        }

        try {
            Artisan::call('db:seed', ['--force' => true]);
        } catch (\Throwable $throwable) {
            Log::info('deploy-seed', ['ip' => $request->ip(), 'succeeded' => false]);

            return response()->json(['ok' => false, 'error' => 'Seed failed', 'detail' => substr($throwable->getMessage(), 0, 2000)], 500);
        }

        Log::info('deploy-seed', ['ip' => $request->ip(), 'succeeded' => true]);

        return response()->json(['ok' => true, 'steps' => [['name' => 'seed', 'ok' => true, 'detail' => substr((string) Artisan::output(), 0, 2000)]]]);
    }

    private function extractArchive(string $archive, string $target): array
    {
        $zip = new ZipArchive();
        $opened = $zip->open($archive);

        if ($opened !== true) {
            return ['ok' => false, 'detail' => 'Cannot open archive'];
        }

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = (string) $zip->getNameIndex($index);
            if ($name === '' || str_starts_with($name, '/') || str_contains($name, '..')) {
                $zip->close();
                return ['ok' => false, 'detail' => 'Unsafe entry: ' . substr($name, 0, 200)];
            }
        }

        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        $extracted = $zip->extractTo($target);
        $zip->close();

        if (!$extracted) {
            return ['ok' => false, 'detail' => 'Extraction failed'];
        }

        return ['ok' => true, 'detail' => 'Extracted ' . basename($archive)];
    }

    private function ensureStorageLink(): array
    {
        $link = (string) public_path('storage');
        $target = (string) storage_path('app/public');

        if (is_link($link) && is_dir($link)) {
            return ['ok' => true, 'detail' => 'exists'];
        }

        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        if (!is_link($link) && !is_file($link) && !is_dir($link)) {
            $linked = @symlink($target, $link);
            if ($linked) {
                return ['ok' => true, 'detail' => 'symlink'];
            }
        }

        try {
            Artisan::call('storage:link');
            if (is_link($link) || is_dir($link)) {
                return ['ok' => true, 'detail' => 'storage:link'];
            }
        } catch (\Throwable $throwable) {
            return ['ok' => true, 'detail' => 'copy-fallback:' . substr($throwable->getMessage(), 0, 200)];
        }

        $copied = $this->copyDirectory($target, $link);

        if ($copied) {
            return ['ok' => true, 'detail' => 'copy-fallback'];
        }

        return ['ok' => false, 'detail' => 'Storage link unavailable'];
    }

    private function copyDirectory(string $source, string $destination): bool
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        foreach ((array) scandir($source) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $from = $source . '/' . $entry;
            $to = $destination . '/' . $entry;
            if (is_dir($from)) {
                $this->copyDirectory($from, $to);
            } else {
                copy($from, $to);
            }
        }

        return true;
    }
}
