<?php

namespace Tests\Feature\Api;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_tags_returns_tag_list_with_post_counts(): void
    {
        Tag::create([
            'name' => 'Research',
            'description' => 'Research updates',
        ]);
        $academic = Tag::create([
            'name' => 'Academic',
            'description' => 'Academic announcements',
        ]);

        $post = Post::create([
            'title' => 'AI Lab Opening',
            'subtitle' => 'New computing resources available',
            'body' => '<p>Lab orientation next Monday.</p>',
            'image' => 'images/DummyImage.png',
        ]);
        $post->tags()->attach($academic);

        $response = $this->getJson('/api/tags');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => ['id', 'slug', 'name', 'description', 'posts_count'],
            ],
        ]);
        $response->assertJsonPath('data.0.name', 'Academic');
        $response->assertJsonPath('data.0.posts_count', 1);
        $response->assertJsonPath('data.1.name', 'Research');
        $response->assertJsonPath('data.1.posts_count', 0);
    }

    public function test_api_tags_includes_tag_without_posts(): void
    {
        Tag::create([
            'name' => 'Beasiswa',
            'description' => 'Scholarship information',
        ]);

        $response = $this->getJson('/api/tags');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name', 'Beasiswa');
        $response->assertJsonPath('data.0.posts_count', 0);
    }

    public function test_api_tags_returns_empty_data_when_no_tags(): void
    {
        $response = $this->getJson('/api/tags');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data', []);
    }

    public function test_api_tags_orders_by_most_recent_post_update(): void
    {
        $older = Tag::create([
            'name' => 'Older',
            'description' => null,
        ]);
        $newer = Tag::create([
            'name' => 'Newer',
            'description' => null,
        ]);

        $olderPost = Post::create([
            'title' => 'Old Announcement',
            'subtitle' => 'Old subtitle',
            'body' => '<p>Old content.</p>',
            'image' => 'images/DummyImage.png',
        ]);
        Post::whereKey($olderPost->id)->update(['created_at' => now()->subDays(20), 'updated_at' => now()->subDays(10)]);
        $older->posts()->attach($olderPost);

        $newerPost = Post::create([
            'title' => 'Fresh Announcement',
            'subtitle' => 'Fresh subtitle',
            'body' => '<p>Fresh content.</p>',
            'image' => 'images/DummyImage.png',
        ]);
        Post::whereKey($newerPost->id)->update(['created_at' => now()->subDays(2), 'updated_at' => now()->subDay()]);
        $newer->posts()->attach($newerPost);

        $response = $this->getJson('/api/tags');

        $response->assertJsonPath('data.0.name', 'Newer');
        $response->assertJsonPath('data.1.name', 'Older');
    }

    public function test_api_tags_places_tags_without_posts_last(): void
    {
        $empty = Tag::create([
            'name' => 'Empty',
            'description' => null,
        ]);
        $fresh = Tag::create([
            'name' => 'Fresh',
            'description' => null,
        ]);

        $post = Post::create([
            'title' => 'New Post',
            'subtitle' => 'New subtitle',
            'body' => '<p>New content.</p>',
            'image' => 'images/DummyImage.png',
        ]);
        $fresh->posts()->attach($post);

        $response = $this->getJson('/api/tags');

        $response->assertJsonPath('data.0.name', 'Fresh');
        $response->assertJsonPath('data.1.name', 'Empty');
    }

    public function test_api_tag_detail_returns_tag_with_posts(): void
    {
        $tag = Tag::create([
            'name' => 'Beasiswa',
            'description' => 'Info beasiswa',
        ]);

        $post = Post::create([
            'title' => 'Pendaftaran Beasiswa 2026',
            'subtitle' => 'Periode baru dibuka',
            'body' => '<p>Detail.</p>',
            'image' => 'images/placeholder.png',
        ]);
        $tag->posts()->attach($post);

        $response = $this->getJson('/api/tags/beasiswa');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'slug',
                'name',
                'description',
                'posts' => [
                    '*' => [
                        'id',
                        'slug',
                        'title',
                        'subtitle',
                        'updated_at',
                        'tags' => ['*' => ['id', 'slug', 'name']],
                    ],
                ],
            ],
        ]);
        $response->assertJsonPath('data.slug', 'beasiswa');
        $response->assertJsonPath('data.name', 'Beasiswa');
        $response->assertJsonCount(1, 'data.posts');
        $response->assertJsonPath('data.posts.0.title', 'Pendaftaran Beasiswa 2026');
        $response->assertJsonPath('data.posts.0.slug', $post->slug);
        $response->assertJsonPath('data.posts.0.tags.0.slug', 'beasiswa');
        $response->assertJsonPath('data.posts.0.updated_at', $post->updated_at->toIso8601String());
        $this->assertArrayNotHasKey('image', $response->json('data.posts.0'));
        $this->assertArrayNotHasKey('posts_count', $response->json('data'));
        $this->assertArrayNotHasKey('body', $response->json('data.posts.0'));
        $this->assertArrayNotHasKey('description', $response->json('data.posts.0.tags.0'));
        $this->assertArrayNotHasKey('posts_count', $response->json('data.posts.0.tags.0'));
    }

    public function test_api_tag_detail_returns_empty_posts_when_tag_has_no_posts(): void
    {
        Tag::create([
            'name' => 'Akademik',
            'description' => 'Info akademik',
        ]);

        $response = $this->getJson('/api/tags/akademik');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data.posts', []);
    }

    public function test_api_tag_detail_returns_404_for_unknown_slug(): void
    {
        $response = $this->getJson('/api/tags/tidak-ada');

        $response->assertStatus(404);
        $response->assertJsonPath('status', 'error');
        $response->assertJsonPath('message', 'Tag not found');
    }

    public function test_api_tag_detail_resolves_by_numeric_id(): void
    {
        $tag = Tag::create([
            'name' => 'Beasiswa',
            'description' => 'Info beasiswa',
        ]);

        $response = $this->getJson('/api/tags/' . $tag->id);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data.slug', 'beasiswa');
    }

    public function test_api_tag_detail_orders_posts_by_newest_updated_at(): void
    {
        $tag = Tag::create([
            'name' => 'Akademik',
            'description' => 'Info akademik',
        ]);

        $olderPost = Post::create([
            'title' => 'Pengumuman Lama',
            'subtitle' => 'Subtitle lama',
            'body' => '<p>Konten lama.</p>',
            'image' => 'images/placeholder.png',
        ]);
        $newerPost = Post::create([
            'title' => 'Pengumuman Baru',
            'subtitle' => 'Subtitle baru',
            'body' => '<p>Konten baru.</p>',
            'image' => 'images/placeholder.png',
        ]);
        Post::whereKey($olderPost->id)->update(['created_at' => now()->subDays(10), 'updated_at' => now()->subDays(5)]);
        Post::whereKey($newerPost->id)->update(['created_at' => now()->subDays(2), 'updated_at' => now()->subDay()]);
        $tag->posts()->attach([$olderPost->id, $newerPost->id]);

        $response = $this->getJson('/api/tags/akademik');

        $response->assertStatus(200);
        $response->assertJsonPath('data.posts.0.title', 'Pengumuman Baru');
        $response->assertJsonPath('data.posts.1.title', 'Pengumuman Lama');
    }
}