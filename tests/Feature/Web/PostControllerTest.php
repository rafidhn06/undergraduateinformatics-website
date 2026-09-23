<?php

namespace Tests\Feature\Web;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_post_detail_route_renders_app_with_initial_data_and_seo_tags(): void
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

        $response = $this->get('/posts/' . $post->slug);

        $response->assertSee('application/ld+json');
        $response->assertSee('Article');
        $response->assertSee('Pendaftaran Beasiswa 2026 - Portal Informasi Sarjana Informatika', false);

        preg_match('/window\.__INITIAL_DATA__ = (\{.*?\});/s', $response->getContent(), $matches);
        $this->assertNotEmpty($matches, 'Initial data script tag not found');
        $initialData = json_decode($matches[1], true);
        $this->assertArrayHasKey('seeds', $initialData);
        $this->assertSame('success', $initialData['seeds'][0]['payload']['status']);
        $this->assertSame('Pendaftaran Beasiswa 2026', $initialData['seeds'][0]['payload']['data']['title']);
        $this->assertSame('beasiswa', $initialData['seeds'][0]['payload']['data']['tags'][0]['slug']);
        $this->assertSame($post->created_at->toIso8601String(), $initialData['seeds'][0]['payload']['data']['created_at']);

        preg_match('/<script type="application\/ld\+json">\s*(\{.*?\})\s*<\/script>/s', $response->getContent(), $ldMatches);
        $this->assertNotEmpty($ldMatches, 'JSON-LD script tag not found');
        $jsonLd = json_decode($ldMatches[1], true);
        $this->assertSame('Article', $jsonLd['@type']);
        $this->assertSame($post->created_at->toIso8601String(), $jsonLd['datePublished']);
        $this->assertSame($post->updated_at->toIso8601String(), $jsonLd['dateModified']);
    }

    public function test_web_post_detail_og_image_falls_back_to_default_banner_when_no_image(): void
    {
        $post = Post::create([
            'title' => 'Post Tanpa Gambar',
            'subtitle' => 'Subtitle',
            'body' => '<p>Detail.</p>',
            'image' => null,
        ]);

        $response = $this->get('/posts/' . $post->slug);

        $response->assertSee('<meta data-ssr="true" property="og:image" content="' . url('/images/banner.jpg') . '">', false);
        $response->assertSee('"image":null', false);
    }

    public function test_web_post_detail_route_resolves_by_id_for_backward_compatibility(): void
    {
        $post = Post::create([
            'title' => 'Pendaftaran Beasiswa 2026',
            'subtitle' => 'Periode baru dibuka',
            'body' => '<p>Detail.</p>',
            'image' => 'images/placeholder.png',
        ]);

        $response = $this->get('/posts/' . $post->id);

        $response->assertStatus(200);
        $response->assertViewIs('app');
    }

    public function test_web_post_detail_route_returns_404_for_missing_slug(): void
    {
        $response = $this->get('/posts/tidak-ada');

        $response->assertStatus(404);
        $response->assertViewIs('app');
        $response->assertSee('"seeds":[]', false);
    }

    public function test_posts_page_renders_with_per_page(): void
    {
        Post::create(['title' => 'Rilis Pertama', 'subtitle' => 'Sub', 'body' => 'Isi']);

        $this->get('/posts?q=Rilis&per_page=5')->assertOk();
    }

    public function test_post_title_cannot_break_out_of_initial_data_script(): void
    {
        $post = Post::create([
            'title' => 'X</script><script>alert(1)</script>Y',
            'subtitle' => 'Sub',
            'body' => '<p>Isi</p>',
        ]);

        $response = $this->get('/posts/' . $post->slug);

        $response->assertStatus(200);
        $response->assertDontSee('X</script><script>alert(1)</script>Y', false);
        $response->assertSee('X\\u003C\\/script\\u003E\\u003Cscript\\u003Ealert(1)\\u003C\\/script\\u003EY', false);
    }
}