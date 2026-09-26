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

    public function test_seeder_preserves_existing_usable_images(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('posts/custom.jpg', 'custom-bytes');
        Post::create([
            'title' => 'Kerja Praktik 2026',
            'subtitle' => 'Sub',
            'body' => '<p>Body</p>',
            'image' => 'posts/custom.jpg',
        ]);

        $this->seed(PostSeeder::class);

        $this->assertSame('posts/custom.jpg', Post::where('title', 'Kerja Praktik 2026')->first()->image);
        Storage::disk('public')->assertExists('posts/custom.jpg');
    }

    public function test_seeder_replaces_missing_image_files(): void
    {
        Storage::fake('public');
        Post::create([
            'title' => 'Kerja Praktik 2026',
            'subtitle' => 'Sub',
            'body' => '<p>Body</p>',
            'image' => 'posts/gone.jpg',
        ]);

        $this->seed(PostSeeder::class);

        $image = Post::where('title', 'Kerja Praktik 2026')->first()->image;
        $this->assertNotSame('posts/gone.jpg', $image);
        $this->assertTrue($image === null || Storage::disk('public')->exists($image));
    }
}