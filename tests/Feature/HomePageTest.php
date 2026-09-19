<?php

namespace Tests\Feature;

use App\Models\DashboardDataset;
use App\Models\DashboardDatasetItem;
use App\Models\ImportantLink;
use App\Models\ImportantSection;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    private function createPost(string $title, string $subtitle, ?\DateTimeInterface $createdAt = null): Post
    {
        $post = Post::create([
            'title' => $title,
            'subtitle' => $subtitle,
            'body' => '<p>Detail.</p>',
            'image' => 'images/DummyImage.png',
        ]);

        if ($createdAt !== null) {
            Post::whereKey($post->id)->update(['created_at' => $createdAt, 'updated_at' => $createdAt]);
            $post = $post->fresh();
        }

        return $post;
    }

    private function createLink(string $name, string $link, ImportantSection $section, ?\DateTimeInterface $updatedAt = null): ImportantLink
    {
        $created = ImportantLink::create([
            'name' => $name,
            'link' => $link,
            'important_section_id' => $section->id,
        ]);

        if ($updatedAt !== null) {
            ImportantLink::whereKey($created->id)->update(['created_at' => $updatedAt, 'updated_at' => $updatedAt]);
            $created = $created->fresh();
        }

        return $created;
    }

    public function test_web_home_route_renders_app_wrapper_with_initial_data_and_seo_tags(): void
    {
        $post = $this->createPost('Welcome to Informatics', 'New semester starting soon');
        $tag = Tag::create(['name' => 'Academic', 'description' => 'Academic announcements']);
        $post->tags()->attach($tag);

        $section = ImportantSection::create(['name' => 'Student Affairs', 'order_number' => 1]);
        $this->createLink('Academic Calendar', 'https://example.com/calendar', $section);

        $dataset = DashboardDataset::create([
            'title' => 'Jumlah Mahasiswa',
            'slug' => 'jumlah-mahasiswa',
            'sheet_name' => 'Sheet1',
            'chart_type' => 'bar',
            'x_label' => 'Tahun',
            'y_label' => 'Mahasiswa',
            'description' => 'Statistik mahasiswa.',
        ]);
        DashboardDatasetItem::create(['dataset_id' => $dataset->id, 'label' => '2024', 'value' => 120, 'sort_order' => 2]);
        DashboardDatasetItem::create(['dataset_id' => $dataset->id, 'label' => '2023', 'value' => 100, 'sort_order' => 1]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('app');
        $response->assertViewHas('initialData');
        $response->assertSee('__INITIAL_DATA__');
        $response->assertSee('Welcome to Informatics');
        $response->assertSee('Academic Calendar');
        $response->assertSee('Beranda - Portal Informasi Sarjana Informatika', false);
        $response->assertSee('Sumber informasi resmi Program Studi Sarjana Informatika Telkom University untuk perkuliahan peserta didik.', false);

        preg_match('/window\.__INITIAL_DATA__ = (\{.*?\});/s', $response->getContent(), $matches);
        $this->assertNotEmpty($matches, 'Initial data script tag not found');
        $initialData = json_decode($matches[1], true);
        $this->assertSame('Welcome to Informatics', $initialData['posts'][0]['title']);
        $this->assertSame('Academic Calendar', $initialData['links'][0]['name']);
        $this->assertSame('Jumlah Mahasiswa', $initialData['datasets'][0]['title']);
        $this->assertSame(['2023', '2024'], $initialData['datasets'][0]['labels']);
        $this->assertSame([100, 120], $initialData['datasets'][0]['values']);
    }
}
