<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }

        DB::table('posts')->where('image', 'like', 'images/posts/%')->orderBy('id')->chunkById(100, function ($posts) {
            foreach ($posts as $post) {
                $old = (string) $post->image;
                $relative = preg_replace('#^images/posts/#', 'posts/', $old);

                if ($relative === $old || $relative === null) {
                    continue;
                }

                if (Storage::disk('public')->exists($relative)) {
                    DB::table('posts')->where('id', $post->id)->update(['image' => $relative]);

                    continue;
                }

                $sourcePath = public_path($old);

                if (! is_file($sourcePath) || ! is_readable($sourcePath)) {
                    continue;
                }

                $contents = file_get_contents($sourcePath);

                if ($contents === false) {
                    continue;
                }

                if (! Storage::disk('public')->put($relative, $contents)) {
                    continue;
                }

                DB::table('posts')->where('id', $post->id)->update(['image' => $relative]);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }

        DB::table('posts')->where('image', 'like', 'posts/%')->orderBy('id')->chunkById(100, function ($posts) {
            foreach ($posts as $post) {
                $current = (string) $post->image;
                $relative = preg_replace('#^posts/#', 'images/posts/', $current);

                if ($relative === $current || $relative === null) {
                    continue;
                }

                if (file_exists(public_path($relative))) {
                    DB::table('posts')->where('id', $post->id)->update(['image' => $relative]);

                    continue;
                }

                if (! Storage::disk('public')->exists($current)) {
                    continue;
                }

                try {
                    $contents = Storage::disk('public')->get($current);
                } catch (\Throwable $e) {
                    continue;
                }

                $targetPath = public_path($relative);
                $targetDirectory = dirname($targetPath);

                if (! is_dir($targetDirectory) && ! mkdir($targetDirectory, 0755, true) && ! is_dir($targetDirectory)) {
                    continue;
                }

                if (file_put_contents($targetPath, $contents) === false) {
                    continue;
                }

                DB::table('posts')->where('id', $post->id)->update(['image' => $relative]);
            }
        });
    }
};
