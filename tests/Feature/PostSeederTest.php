<?php

namespace Tests\Feature;

use App\Models\Post;
use Database\Seeders\PostSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_writes_a_mix_of_null_and_random_local_images(): void
    {
        Storage::fake('public');

        $this->seed(PostSeeder::class);

        $nullImages = Post::whereNull('image')->count();
        $withImages = Post::whereNotNull('image')->count();

        $this->assertGreaterThan(0, $nullImages);
        $this->assertGreaterThan(0, $withImages);

        $images = Post::whereNotNull('image')->pluck('image');

        foreach ($images as $image) {
            $this->assertMatchesRegularExpression('#^images/posts/post-\d{2}\.jpg$#', $image);
            $this->assertTrue(Storage::disk('public')->exists($image));
        }
    }

    public function test_seeder_writes_rich_text_bodies(): void
    {
        $this->seed(PostSeeder::class);

        $posts = Post::all();

        foreach ($posts as $post) {
            $this->assertMatchesRegularExpression(
                '/<(p|h[1-6]|ul|ol|table)[\s>]/',
                $post->body,
                "Post '{$post->title}' body should be rich text with a block-level element"
            );
        }
    }
}