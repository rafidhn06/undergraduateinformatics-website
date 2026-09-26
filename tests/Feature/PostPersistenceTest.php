<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostPersistenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        Tag::create(['name' => 'S1 Informatika']);
    }

    private function makePost(): Post
    {
        return Post::create([
            'title' => 'Judul',
            'subtitle' => 'Sub',
            'body' => '<p>Body</p>',
        ]);
    }

    public function test_sync_tags_attaches_given_tags_plus_default_and_dedupes(): void
    {
        $tag = Tag::create(['name' => 'Beasiswa']);
        $post = $this->makePost();

        $post->syncTags([$tag->id, $tag->id]);

        $this->assertEqualsCanonicalizing(
            ['S1 Informatika', 'Beasiswa'],
            $post->tags()->pluck('name')->all()
        );
    }

    public function test_sync_tags_replaces_previous_tags_but_keeps_default(): void
    {
        $first = Tag::create(['name' => 'Beasiswa']);
        $second = Tag::create(['name' => 'Kalender']);
        $post = $this->makePost();
        $post->syncTags([$first->id]);

        $post->syncTags([$second->id]);

        $this->assertEqualsCanonicalizing(
            ['S1 Informatika', 'Kalender'],
            $post->tags()->pluck('name')->all()
        );
    }

    public function test_replace_image_stores_file_and_replaces_old_one(): void
    {
        $post = $this->makePost();

        $post->replaceImage(UploadedFile::fake()->image('first.jpg'));
        $post->save();
        $first = $post->image;

        $this->assertNotNull($first);
        Storage::disk('public')->assertExists($first);

        $post->replaceImage(UploadedFile::fake()->image('second.jpg'));
        $post->save();

        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($post->image);
    }

    public function test_replace_image_with_remove_clears_image_and_file(): void
    {
        $post = $this->makePost();
        $post->replaceImage(UploadedFile::fake()->image('photo.jpg'));
        $post->save();
        $path = $post->image;

        $post->replaceImage(null, true);
        $post->save();

        $this->assertNull($post->image);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_deleting_post_removes_its_image_file(): void
    {
        $post = $this->makePost();
        $post->replaceImage(UploadedFile::fake()->image('photo.jpg'));
        $post->save();
        $path = $post->image;

        $post->delete();

        Storage::disk('public')->assertMissing($path);
    }

    public function test_replace_image_keeps_file_shared_with_another_post(): void
    {
        Storage::disk('public')->put('posts/shared.jpg', 'shared-bytes');
        $first = Post::create(['title' => 'Pertama', 'subtitle' => 'Sub', 'body' => '<p>Body</p>', 'image' => 'posts/shared.jpg']);
        $second = Post::create(['title' => 'Kedua', 'subtitle' => 'Sub', 'body' => '<p>Body</p>', 'image' => 'posts/shared.jpg']);

        $first->replaceImage(UploadedFile::fake()->image('baru.jpg'));
        $first->save();

        Storage::disk('public')->assertExists('posts/shared.jpg');
        $this->assertSame('posts/shared.jpg', $second->fresh()->image);
    }

    public function test_deleting_post_keeps_file_shared_with_another_post(): void
    {
        Storage::disk('public')->put('posts/shared.jpg', 'shared-bytes');
        $first = Post::create(['title' => 'Pertama', 'subtitle' => 'Sub', 'body' => '<p>Body</p>', 'image' => 'posts/shared.jpg']);
        Post::create(['title' => 'Kedua', 'subtitle' => 'Sub', 'body' => '<p>Body</p>', 'image' => 'posts/shared.jpg']);

        $first->delete();

        Storage::disk('public')->assertExists('posts/shared.jpg');
    }
}
