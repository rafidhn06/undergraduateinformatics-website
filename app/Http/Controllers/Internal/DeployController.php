<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use Illuminate\Database\Seeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
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

        $seedClass = $request->input('seed_class');
        $seedClass = is_string($seedClass) && $seedClass !== '' ? $seedClass : null;

        if ($seedClass !== null && !$this->isSeedable($seedClass)) {
            return response()->json(['ok' => false, 'error' => 'Invalid seeder class'], 422);
        }

        $pendingMigrations = $this->pendingMigrationCount();

        $steps = [];
        $clock = microtime(true);

        register_shutdown_function(function () {
            try {
                Artisan::call('up');
            } catch (\Throwable) {
            }
        });

        $only = array_values(array_filter((array) $request->input('only', []), 'is_string'));
        $quick = $only !== [];
        $run = fn (string $step): bool => !$quick || in_array($step, $only, true);
        $ready = fn (array $step): bool => $quick || $step['ok'];

        try {
            Artisan::call('down', ['--render' => 'errors::503']);
        } catch (\Throwable $throwable) {
            $this->recordStep($steps, $clock, 'down', false, substr($throwable->getMessage(), 0, 500));
        }

        $extractedApp = ['ok' => true, 'detail' => 'Skipped'];
        if ($run('extract-app')) {
            $extractedApp = $this->extractArchive($appArchive, $appPath);
        }
        $this->recordStep($steps, $clock, 'extract-app', $extractedApp['ok'], $extractedApp['detail']);

        $extractedPublic = ['ok' => true, 'detail' => 'Skipped'];
        if ($run('extract-public') && $extractedApp['ok']) {
            $extractedPublic = $this->extractArchive($publicArchive, $publicPath, true);
        }
        $this->recordStep($steps, $clock, 'extract-public', $extractedPublic['ok'], $extractedPublic['detail']);

        if ($run('backup') && $extractedApp['ok'] && $extractedPublic['ok'] && !$rollback) {
            try {
                $backupApp = $directory . '/app-prev.zip';
                $backupPublic = $directory . '/public-prev.zip';
                if (is_file($appArchive) && !is_file($backupApp)) {
                    copy($appArchive, $backupApp);
                }
                if (is_file($publicArchive) && !is_file($backupPublic)) {
                    copy($publicArchive, $backupPublic);
                }
                $this->recordStep($steps, $clock, 'backup', true, 'Previous archives retained');
            } catch (\Throwable $throwable) {
                $this->recordStep($steps, $clock, 'backup', false, substr($throwable->getMessage(), 0, 500));
            }
        }

        $migrated = ['ok' => true, 'detail' => 'Skipped'];
        if ($run('migrate') && $ready($extractedApp) && $ready($extractedPublic)) {
            try {
                Artisan::call('migrate', ['--force' => true]);
                $migrated = ['ok' => true, 'detail' => substr((string) Artisan::output(), 0, 2000)];
            } catch (\Throwable $throwable) {
                $migrated = ['ok' => false, 'detail' => substr($throwable->getMessage(), 0, 2000)];
            }
        }
        $this->recordStep($steps, $clock, 'migrate', $migrated['ok'], $migrated['detail']);

        $link = ['ok' => true, 'detail' => 'Skipped'];
        if ($run('storage-link') && $ready($migrated)) {
            $link = $this->ensureStorageLink();
        }
        $this->recordStep($steps, $clock, 'storage-link', $link['ok'], $link['detail']);

        $seeded = ['ok' => true, 'detail' => 'Skipped'];
        $seedRequested = $seedClass !== null || (!$quick && $request->boolean('seed')) || ($quick && $run('seed'));
        if ($seedRequested && $ready($migrated)) {
            try {
                if ($seedClass !== null) {
                    Artisan::call('db:seed', ['--class' => $seedClass, '--force' => true]);
                } else {
                    Artisan::call('db:seed', ['--force' => true]);
                }
                $seeded = ['ok' => true, 'detail' => substr((string) Artisan::output(), 0, 2000)];
            } catch (\Throwable $throwable) {
                $seeded = ['ok' => false, 'detail' => substr($throwable->getMessage(), 0, 2000)];
            }
        }
        $this->recordStep($steps, $clock, 'seed', $seeded['ok'], $seeded['detail']);

        if ($run('caches') && $ready($migrated)) {
            foreach (['config:clear', 'config:cache', 'route:cache', 'view:cache'] as $command) {
                try {
                    Artisan::call(str_contains($command, ':') ? explode(':', $command)[0] . ':' . explode(':', $command)[1] : $command);
                    $this->recordStep($steps, $clock, $command, true, substr((string) Artisan::output(), 0, 500));
                } catch (\Throwable $throwable) {
                    $this->recordStep($steps, $clock, $command, false, substr($throwable->getMessage(), 0, 500));
                }
            }
        }

        try {
            Artisan::call('up');
        } catch (\Throwable $throwable) {
            $this->recordStep($steps, $clock, 'up', false, substr($throwable->getMessage(), 0, 500));
        }

        $succeeded = collect($steps)->every(fn ($step) => $step['ok'] === true);

        Log::info('deploy', [
            'rollback' => $rollback,
            'ip' => $request->ip(),
            'succeeded' => $succeeded,
            'steps' => collect($steps)->map(fn ($step) => $step['name'] . ':' . ($step['ok'] ? 'ok' : 'fail'))->values()->all(),
        ]);

        return response()->json(['ok' => $succeeded, 'pending_migrations' => $pendingMigrations, 'steps' => $steps], $succeeded ? 200 : 500);
    }

    private function runSeed(Request $request): JsonResponse
    {
        try {
            Artisan::call('db:seed', ['--force' => true]);
        } catch (\Throwable $throwable) {
            Log::info('deploy-seed', ['ip' => $request->ip(), 'succeeded' => false]);

            return response()->json(['ok' => false, 'error' => 'Seed failed', 'detail' => substr($throwable->getMessage(), 0, 2000)], 500);
        }

        Log::info('deploy-seed', ['ip' => $request->ip(), 'succeeded' => true]);

        return response()->json(['ok' => true, 'steps' => [['name' => 'seed', 'ok' => true, 'detail' => substr((string) Artisan::output(), 0, 2000)]]]);
    }

    private function extractArchive(string $archive, string $target, bool $preserveStorageLink = false): array
    {
        $zip = new ZipArchive();
        $opened = $zip->open($archive);

        if ($opened !== true) {
            return ['ok' => false, 'detail' => 'Cannot open archive'];
        }

        $entries = [];

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = (string) $zip->getNameIndex($index);
            if ($name === '' || str_starts_with($name, '/') || str_contains($name, '..')) {
                $zip->close();
                return ['ok' => false, 'detail' => 'Unsafe entry: ' . substr($name, 0, 200)];
            }
            if ($preserveStorageLink && ($name === 'storage' || str_starts_with($name, 'storage/'))) {
                continue;
            }
            $entries[] = $name;
        }

        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        $extracted = $zip->extractTo($target, $entries);
        $zip->close();

        if (!$extracted) {
            return ['ok' => false, 'detail' => 'Extraction failed'];
        }

        return ['ok' => true, 'detail' => 'Extracted ' . basename($archive)];
    }

    private function ensureStorageLink(?string $link = null, ?string $target = null): array
    {
        $link ??= (string) public_path('storage');
        $target ??= (string) storage_path('app/public');

        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        if ($this->pointsAt($link, $target)) {
            return ['ok' => true, 'detail' => 'exists'];
        }

        if (is_link($link) || is_file($link)) {
            @unlink($link);
        } elseif (is_dir($link)) {
            File::deleteDirectory($link);
        }

        if (!$this->symlinkAvailable()) {
            $serving = Route::has('storage.file');

            return ['ok' => $serving, 'detail' => $serving ? 'php-serving' : 'storage unreachable'];
        }

        try {
            $linked = @\symlink($target, $link);
        } catch (\Throwable) {
            $linked = false;
        }

        if ($linked && $this->pointsAt($link, $target)) {
            return ['ok' => true, 'detail' => 'symlink'];
        }

        try {
            Artisan::call('storage:link');
        } catch (\Throwable $throwable) {
            return ['ok' => false, 'detail' => 'link failed:' . substr($throwable->getMessage(), 0, 200)];
        }

        if ($this->pointsAt($link, $target)) {
            return ['ok' => true, 'detail' => 'storage:link'];
        }

        return ['ok' => false, 'detail' => 'link failed: storage unreachable'];
    }

    private function isSeedable(string $class): bool
    {
        return class_exists($class)
            && is_subclass_of($class, Seeder::class)
            && str_starts_with($class, 'Database\\Seeders\\');
    }

    private function pendingMigrationCount(): int
    {
        try {
            $ran = app('migration.repository')->getRan();
            $files = app('migrator')->getMigrationFiles(database_path('migrations'));

            return count(array_diff(array_keys($files), $ran));
        } catch (\Throwable) {
            return -1;
        }
    }

    private function recordStep(array &$steps, float &$clock, string $name, bool $ok, string $detail): void
    {
        $now = microtime(true);
        $steps[] = ['name' => $name, 'ok' => $ok, 'detail' => $detail, 'duration_ms' => (int) round(($now - $clock) * 1000)];
        $clock = $now;
    }

    protected function symlinkAvailable(): bool
    {
        return function_exists('symlink');
    }

    private function pointsAt(string $link, string $target): bool
    {
        if (!is_link($link)) {
            return false;
        }

        $resolvedLink = realpath($link);
        $resolvedTarget = realpath($target);

        return $resolvedLink !== false && $resolvedTarget !== false && $resolvedLink === $resolvedTarget;
    }
}
