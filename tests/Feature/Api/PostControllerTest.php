<?php

namespace Tests\Feature\Api;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createPost(string $title, string $subtitle, string $body, ?\DateTimeInterface $updatedAt = null): Post
    {
        $post = Post::create([
            'title' => $title,
            'subtitle' => $subtitle,
            'body' => $body,
            'image' => 'images/placeholder.png',
        ]);

        if ($updatedAt !== null) {
            Post::whereKey($post->id)->update(['created_at' => $updatedAt, 'updated_at' => $updatedAt]);
            $post = $post->fresh();
        }

        return $post;
    }

    public function test_posts_index_accepts_per_page(): void
    {
        Post::create(['title' => 'Rilis Pertama', 'subtitle' => 'Sub', 'body' => 'Isi']);
        Post::create(['title' => 'Rilis Kedua', 'subtitle' => 'Sub', 'body' => 'Isi']);

        $response = $this->getJson('/api/posts?per_page=1&page=1');

        $response->assertOk();
        $response->assertJsonPath('status', 'success');
        $this->assertCount(1, $response->json('data'));
        $this->assertSame(1, $response->json('meta.per_page'));
    }

    public function test_api_post_detail_returns_post_json_structure(): void
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
        $post->tags()->attach($tag);

        $response = $this->getJson('/api/posts/' . $post->slug);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'slug',
                'title',
                'subtitle',
                'body',
                'image',
                'created_at',
                'updated_at',
                'tags' => ['*' => ['id', 'slug', 'name']],
            ],
        ]);
        $response->assertJsonPath('data.slug', $post->slug);
        $response->assertJsonPath('data.title', 'Pendaftaran Beasiswa 2026');
        $response->assertJsonPath('data.tags.0.slug', 'beasiswa');
        $response->assertJsonPath('data.created_at', $post->created_at->toIso8601String());
        $this->assertArrayNotHasKey('description', $response->json('data.tags.0'));
    }

    public function test_api_post_detail_resolves_by_numeric_id(): void
    {
        $post = Post::create([
            'title' => 'Pendaftaran Beasiswa 2026',
            'subtitle' => 'Periode baru dibuka',
            'body' => '<p>Detail.</p>',
            'image' => 'images/placeholder.png',
        ]);

        $response = $this->getJson('/api/posts/' . $post->id);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data.slug', $post->slug);
    }

    public function test_api_post_detail_returns_null_image_when_file_is_missing(): void
    {
        Storage::fake('public');

        $post = Post::create([
            'title' => 'Pendaftaran Beasiswa 2026',
            'subtitle' => 'Periode baru dibuka',
            'body' => '<p>Detail.</p>',
            'image' => 'images/placeholder.png',
        ]);

        $response = $this->getJson('/api/posts/' . $post->slug);

        $response->assertStatus(200);
        $response->assertJsonPath('data.image', null);
    }

    public function test_api_post_detail_returns_image_url_when_file_exists(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('images/placeholder.png', 'fake-image-bytes');

        $post = Post::create([
            'title' => 'Pendaftaran Beasiswa 2026',
            'subtitle' => 'Periode baru dibuka',
            'body' => '<p>Detail.</p>',
            'image' => 'images/placeholder.png',
        ]);

        $response = $this->getJson('/api/posts/' . $post->slug);

        $response->assertStatus(200);
        $response->assertJsonPath('data.image', asset('storage/images/placeholder.png'));
    }

    public function test_api_post_detail_returns_404_for_unknown_slug(): void
    {
        $response = $this->getJson('/api/posts/tidak-ada');

        $response->assertStatus(404);
        $response->assertJsonPath('status', 'error');
        $response->assertJsonPath('message', 'Post not found');
    }

    public function test_api_posts_matches_title(): void
    {
        $post = $this->createPost('Pendaftaran Beasiswa 2026', 'Periode baru dibuka', '<p>Detail.</p>');

        $response = $this->getJson('/api/posts?q=beasiswa');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', 'Pendaftaran Beasiswa 2026');
        $response->assertJsonPath('data.0.slug', $post->slug);
    }

    public function test_api_posts_matches_subtitle(): void
    {
        $this->createPost('Pendaftaran Beasiswa 2026', 'Periode baru dibuka', '<p>Detail.</p>');

        $response = $this->getJson('/api/posts?q=periode');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.title', 'Pendaftaran Beasiswa 2026');
    }

    public function test_api_posts_matches_body(): void
    {
        $this->createPost('Pendaftaran Beasiswa 2026', 'Periode baru dibuka', '<p>Jadwal seleksi Mei.</p>');

        $response = $this->getJson('/api/posts?q=seleksi');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.title', 'Pendaftaran Beasiswa 2026');
    }

    public function test_api_posts_returns_empty_when_no_match(): void
    {
        $this->createPost('Pendaftaran Beasiswa 2026', 'Periode baru dibuka', '<p>Detail.</p>');

        $response = $this->getJson('/api/posts?q=tidakada');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data', []);
        $response->assertJsonPath('meta.total', 0);
    }

    public function test_api_posts_with_empty_q_returns_all_posts_ordered_by_updated_at_desc(): void
    {
        $this->createPost('Pengumuman Lama', 'Subtitle lama', '<p>Konten lama.</p>', now()->subDays(5));
        $this->createPost('Pengumuman Baru', 'Subtitle baru', '<p>Konten baru.</p>', now()->subDay());

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.title', 'Pengumuman Baru');
        $response->assertJsonPath('data.1.title', 'Pengumuman Lama');
        $response->assertJsonPath('meta.total', 2);
    }

    public function test_api_posts_escapes_like_wildcards(): void
    {
        $this->createPost('Diskon 100%', 'Promo terbatas', '<p>Konten.</p>', now()->subDay());
        $this->createPost('Diskon 100', 'Promo terbatas', '<p>Konten.</p>', now()->subHours(2));

        $response = $this->getJson('/api/posts?q=100%');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', 'Diskon 100%');
    }

    public function test_api_posts_percent_only_returns_empty(): void
    {
        $this->createPost('Beasiswa', 'Periode baru dibuka', '<p>Detail.</p>');

        $response = $this->getJson('/api/posts?q=%');

        $response->assertStatus(200);
        $response->assertJsonPath('data', []);
        $response->assertJsonPath('meta.total', 0);
    }

    public function test_api_posts_with_present_empty_q_returns_all_posts(): void
    {
        $this->createPost('Pengumuman Lama', 'Subtitle lama', '<p>Konten lama.</p>', now()->subDays(5));
        $this->createPost('Pengumuman Baru', 'Subtitle baru', '<p>Konten baru.</p>', now()->subDay());

        $response = $this->getJson('/api/posts?q=');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.title', 'Pengumuman Baru');
        $response->assertJsonPath('data.1.title', 'Pengumuman Lama');
        $response->assertJsonPath('meta.total', 2);
    }

    public function test_api_posts_paginates_results(): void
    {
        foreach (range(1, 5) as $i) {
            $this->createPost("Pengumuman ke-$i", "Subtitle $i", '<p>Konten.</p>', now()->subMinutes(5 - $i));
        }

        $response = $this->getJson('/api/posts?per_page=2&page=2');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.title', 'Pengumuman ke-3');
        $response->assertJsonPath('data.1.title', 'Pengumuman ke-2');
        $response->assertJsonPath('meta.current_page', 2);
        $response->assertJsonPath('meta.per_page', 2);
        $response->assertJsonPath('meta.total', 5);
        $response->assertJsonPath('meta.last_page', 3);
    }

    public function test_api_posts_normalizes_page_and_per_page(): void
    {
        $this->createPost('Pengumuman', 'Subtitle', '<p>Konten.</p>', now()->subDay());
        $this->createPost('Pengumuman 2', 'Subtitle 2', '<p>Konten.</p>', now()->subHours(2));

        $response = $this->getJson('/api/posts?page=0&per_page=999');

        $response->assertStatus(200);
        $response->assertJsonPath('meta.current_page', 1);
        $response->assertJsonPath('meta.per_page', 50);
        $response->assertJsonCount(2, 'data');
    }

    public function test_api_posts_returns_summary_shape_without_body(): void
    {
        $tag = Tag::create(['name' => 'Beasiswa', 'description' => 'Info beasiswa']);
        $post = $this->createPost('Pendaftaran Beasiswa 2026', 'Periode baru dibuka', '<p>Detail.</p>');
        $post->tags()->attach($tag);

        $response = $this->getJson('/api/posts?q=beasiswa');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id',
                    'slug',
                    'title',
                    'subtitle',
                    'updated_at',
                    'tags' => ['*' => ['id', 'slug', 'name']],
                ],
            ],
            'meta' => ['current_page', 'per_page', 'total', 'last_page'],
        ]);
        $this->assertArrayNotHasKey('body', $response->json('data.0'));
        $this->assertArrayNotHasKey('image', $response->json('data.0'));
        $response->assertJsonPath('data.0.tags.0.slug', 'beasiswa');
    }

    public function test_api_posts_uses_single_character_escape_clause(): void
    {
        $this->createPost('Pendaftaran Beasiswa 2026', 'Periode baru dibuka', '<p>Detail.</p>');

        DB::enableQueryLog();
        $this->getJson('/api/posts?q=beasiswa');
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $this->assertNotEmpty($queries);
        $likeQuery = collect($queries)->first(fn (array $query) => str_contains($query['query'], 'ESCAPE'));
        $this->assertNotNull($likeQuery, 'A LIKE ... ESCAPE query should run');
        $this->assertStringContainsString("ESCAPE '!'", $likeQuery['query']);
        $this->assertStringNotContainsString("ESCAPE '\\'", $likeQuery['query']);
    }

    public function test_web_search_route_redirects_to_posts_index(): void
    {
        $this->createPost('Pendaftaran Beasiswa 2026', 'Periode baru dibuka', '<p>Detail.</p>');

        $this->get('/posts/search?q=beasiswa')->assertRedirect('/posts?q=beasiswa');
    }
}
