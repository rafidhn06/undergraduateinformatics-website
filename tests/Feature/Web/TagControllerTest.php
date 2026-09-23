<?php

namespace Tests\Feature\Web;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_tags_route_renders_app_wrapper_with_initial_data_and_seo_tags(): void
    {
        Tag::create([
            'name' => 'Academic',
            'description' => 'Academic announcements',
        ]);

        $response = $this->get('/tags');

        $response->assertSee('application/ld+json');
        $response->assertSee('CollectionPage');
        $response->assertSee('Daftar Topik - Portal Informasi Sarjana Informatika', false);
        $response->assertSee('Kumpulan topik informasi perkuliahan peserta didik Program Studi Sarjana Informatika Telkom University', false);

        preg_match('/window\.__INITIAL_DATA__ = (\{.*?\});/s', $response->getContent(), $matches);
        $this->assertNotEmpty($matches, 'Initial data script tag not found');
        $initialData = json_decode($matches[1], true);
        $this->assertArrayHasKey('seeds', $initialData);
        $this->assertSame('success', $initialData['seeds'][0]['payload']['status']);
        $this->assertSame('Academic', $initialData['seeds'][0]['payload']['data'][0]['name']);
        $this->assertSame(0, $initialData['seeds'][0]['payload']['data'][0]['posts_count']);
    }

    public function test_web_tag_detail_route_renders_app_with_initial_data_and_seo_tags(): void
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

        $response = $this->get('/tags/beasiswa');

        $response->assertSee('application/ld+json');
        $response->assertSee('CollectionPage');

        preg_match('/window\.__INITIAL_DATA__ = (\{.*?\});/s', $response->getContent(), $matches);
        $this->assertNotEmpty($matches, 'Initial data script tag not found');
        $initialData = json_decode($matches[1], true);
        $this->assertArrayHasKey('seeds', $initialData);
        $this->assertSame('success', $initialData['seeds'][0]['payload']['status']);
        $this->assertSame('Beasiswa', $initialData['seeds'][0]['payload']['data']['name']);
        $this->assertSame('Pendaftaran Beasiswa 2026', $initialData['seeds'][0]['payload']['data']['posts'][0]['title']);
        $this->assertSame('pendaftaran-beasiswa-2026', $initialData['seeds'][0]['payload']['data']['posts'][0]['slug']);
        $this->assertSame('beasiswa', $initialData['seeds'][0]['payload']['data']['posts'][0]['tags'][0]['slug']);
    }

    public function test_web_tag_detail_route_resolves_by_id_for_backward_compatibility(): void
    {
        $tag = Tag::create([
            'name' => 'Beasiswa',
            'description' => 'Info beasiswa',
        ]);

        $response = $this->get('/tags/' . $tag->id);

        $response->assertStatus(200);
        $response->assertViewIs('app');
        $response->assertSee('Beasiswa');
    }

    public function test_web_tag_detail_route_returns_404_for_missing_slug(): void
    {
        $response = $this->get('/tags/tidak-ada');

        $response->assertStatus(404);
        $response->assertViewIs('app');
        $response->assertSee('"seeds":[]', false);
    }
}