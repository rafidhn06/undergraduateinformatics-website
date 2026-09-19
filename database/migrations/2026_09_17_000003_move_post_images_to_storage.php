<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('posts')->where('image', 'like', 'images/posts/%')->orderBy('id')->chunkById(100, function ($posts) {
            foreach ($posts as $post) {
                $old = (string) $post->image;
                $relative = preg_replace('#^images/posts/#', 'posts/', $old);
                if (file_exists(public_path($old)) && ! Storage::disk('public')->exists($relative)) {
                    Storage::disk('public')->put($relative, file_get_contents(public_path($old)));
                }
                DB::table('posts')->where('id', $post->id)->update(['image' => $relative]);
            }
        });
    }

    public function down(): void
    {
        DB::table('posts')->where('image', 'like', 'posts/%')->orderBy('id')->chunkById(100, function ($posts) {
            foreach ($posts as $post) {
                DB::table('posts')->where('id', $post->id)->update(['image' => preg_replace('#^posts/#', 'images/posts/', (string) $post->image)]);
            }
        });
    }
};
