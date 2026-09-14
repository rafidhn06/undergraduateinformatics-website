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
        return Post::create([
            'title' => $title,
            'subtitle' => $subtitle,
            'body' => '<p>Detail.</p>',
            'image' => 'images/DummyImage.png',
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    private function createLink(string $name, string $link, ImportantSection $section, ?\DateTimeInterface $updatedAt = null): ImportantLink
    {
        return ImportantLink::create([
            'name' => $name,
            'link' => $link,
            'important_section_id' => $section->id,
            'created_at' => $updatedAt,
            'updated_at' => $updatedAt,
        ]);
    }

    public function test_web_home_route_renders_app_wrapper_with_initial_data_and_seo_tags(): void
    {
        $post = $this->createPost('Welcome to Informatics', 'New semester starting soon');
        $tag = Tag::create(['name' => 'Academic', 'description' => 'Academic announcements']);
        $post->tags()->attach($tag);

        $section = ImportantSection::create(['name' => 'Student Affairs', 'order_number' => 1]);
        $this->createLink('Academic Calendar', 'https://example.com/calendar', $section);

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
        $this->assertSame('success', $initialData['status']);
        $this->assertSame('Welcome to Informatics', $initialData['data']['latest_posts'][0]['title']);
        $this->assertSame('Academic Calendar', $initialData['data']['latest_links'][0]['name']);
        $this->assertArrayHasKey('dashboard', $initialData['data']);
    }

    public function test_api_home_returns_new_payload_structure(): void
    {
        $post = $this->createPost('AI Lab Opening', 'New computing resources available');
        $tag = Tag::create(['name' => 'Research', 'description' => 'Research updates']);
        $post->tags()->attach($tag);

        $section = ImportantSection::create(['name' => 'Curriculum', 'order_number' => 1]);
        $this->createLink('Study Plan', 'https://example.com/study-plan', $section);

        $response = $this->getJson('/api/home');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonStructure([
            'status',
            'data' => [
                'latest_posts' => [
                    '*' => [
                        'id',
                        'slug',
                        'title',
                        'subtitle',
                        'updated_at',
                        'tags' => ['*' => ['id', 'slug', 'name']],
                    ],
                ],
                'latest_links' => [
                    '*' => [
                        'id',
                        'name',
                        'link',
                        'updated_at',
                        'section' => ['id', 'name'],
                    ],
                ],
                'dashboard',
            ],
        ]);
        $response->assertJsonPath('data.latest_posts.0.title', 'AI Lab Opening');
        $response->assertJsonPath('data.latest_posts.0.tags.0.name', 'Research');
        $response->assertJsonPath('data.latest_links.0.name', 'Study Plan');
        $response->assertJsonPath('data.latest_links.0.section.name', 'Curriculum');
        $this->assertArrayNotHasKey('body', $response->json('data.latest_posts.0'));
        $this->assertArrayNotHasKey('image', $response->json('data.latest_posts.0'));
    }

    public function test_api_home_returns_only_five_latest_posts(): void
    {
        foreach (range(1, 6) as $i) {
            $this->createPost("Post ke-$i", "Subtitle $i", now()->subMinutes(6 - $i));
        }

        $response = $this->getJson('/api/home');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data.latest_posts');
        $response->assertJsonPath('data.latest_posts.0.title', 'Post ke-6');
        $response->assertJsonPath('data.latest_posts.4.title', 'Post ke-2');
    }

    public function test_api_home_returns_only_five_latest_links_with_section(): void
    {
        $section = ImportantSection::create(['name' => 'Kemahasiswaan', 'order_number' => 1]);
        foreach (range(1, 6) as $i) {
            $this->createLink("Link ke-$i", "https://example.com/$i", $section, now()->subMinutes(6 - $i));
        }

        $response = $this->getJson('/api/home');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data.latest_links');
        $response->assertJsonPath('data.latest_links.0.name', 'Link ke-6');
        $response->assertJsonPath('data.latest_links.4.name', 'Link ke-2');
        $response->assertJsonPath('data.latest_links.0.section.name', 'Kemahasiswaan');
    }

    public function test_api_home_returns_dashboard_datasets_with_labels_and_values(): void
    {
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

        $response = $this->getJson('/api/home');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'dashboard' => [
                    '*' => ['id', 'title', 'chart_type', 'x_label', 'y_label', 'labels', 'values'],
                ],
            ],
        ]);
        $response->assertJsonPath('data.dashboard.0.title', 'Jumlah Mahasiswa');
        $response->assertJsonPath('data.dashboard.0.chart_type', 'bar');
        $response->assertJsonPath('data.dashboard.0.labels', ['2023', '2024']);
        $response->assertJsonPath('data.dashboard.0.values', [100, 120]);
    }
}
