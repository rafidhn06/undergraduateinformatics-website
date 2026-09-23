<?php

namespace Tests\Feature\Web;

use App\Models\DashboardDataset;
use App\Models\DashboardDatasetItem;
use App\Models\ImportantLink;
use App\Models\ImportantSection;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeedContractTest extends TestCase
{
    use RefreshDatabase;

    private function seedData(): void
    {
        $post = Post::create([
            'title' => 'Kontrak Seed',
            'subtitle' => 'Sub',
            'body' => '<p>Isi.</p>',
            'image' => 'images/DummyImage.png',
        ]);
        $tag = Tag::create(['name' => 'Akademik', 'description' => 'Info']);
        $post->tags()->attach($tag);

        $section = ImportantSection::create(['name' => 'Layanan', 'order_number' => 1]);
        ImportantLink::create([
            'name' => 'Kalender',
            'link' => 'https://example.com/kalender',
            'important_section_id' => $section->id,
        ]);

        $dataset = DashboardDataset::create([
            'title' => 'Jumlah',
            'slug' => 'jumlah',
            'sheet_name' => 'Sheet1',
            'chart_type' => 'bar',
            'description' => 'Stat.',
        ]);
        DashboardDatasetItem::create(['dataset_id' => $dataset->id, 'label' => '2024', 'value' => 5, 'sort_order' => 1]);
    }

    private function seeds(string $url): array
    {
        $response = $this->get($url);
        $response->assertOk();
        preg_match('/window\.__INITIAL_DATA__ = (\{.*?\});/s', $response->getContent(), $matches);
        $this->assertNotEmpty($matches);

        return json_decode($matches[1], true)['seeds'];
    }

    public function test_home_seeds_match_api_responses(): void
    {
        $this->seedData();
        $seeds = $this->seeds('/');
        $this->assertCount(3, $seeds);

        $byEndpoint = [];
        foreach ($seeds as $seed) {
            $byEndpoint[$seed['endpoint'].json_encode($seed['params'] ?? [])] = $seed['payload'];
        }

        $postsApi = $this->getJson('/api/posts?per_page=5')->json();
        $this->assertEquals($postsApi, $byEndpoint['/api/posts'.json_encode(['per_page' => 5])]);

        $linksApi = $this->getJson('/api/important-links?per_page=5')->json();
        $this->assertEquals($linksApi, $byEndpoint['/api/important-links'.json_encode(['per_page' => 5])]);

        $datasetsApi = $this->getJson('/api/datasets')->json();
        $this->assertEquals($datasetsApi, $byEndpoint['/api/datasets'.json_encode([])]);
    }
}
