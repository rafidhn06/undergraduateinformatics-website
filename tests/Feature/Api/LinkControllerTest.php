<?php

namespace Tests\Feature\Api;

use App\Models\ImportantLink;
use App\Models\ImportantSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_links_returns_sections_grouped_with_nested_links(): void
    {
        $first = ImportantSection::create(['name' => 'Kumpulan Link MBKM', 'order_number' => 1]);
        $second = ImportantSection::create(['name' => 'Kumpulan Link Kelas', 'order_number' => 2]);

        ImportantLink::create(['important_section_id' => $first->id, 'name' => 'Angkatan 2020', 'link' => 'http://bit.ly/MBKM2020']);
        ImportantLink::create(['important_section_id' => $second->id, 'name' => 'Angkatan 2019', 'link' => 'http://bit.ly/Kelas2019']);

        $response = $this->getJson('/api/link-sections');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => ['id', 'name', 'order_number', 'links' => ['*' => ['id', 'name', 'link', 'updated_at']]],
            ],
        ]);
        $response->assertJsonPath('data.0.name', 'Kumpulan Link MBKM');
        $response->assertJsonPath('data.0.links.0.name', 'Angkatan 2020');
        $response->assertJsonPath('data.1.name', 'Kumpulan Link Kelas');
        $response->assertJsonPath('data.1.links.0.name', 'Angkatan 2019');
    }

    public function test_api_links_orders_sections_by_order_number(): void
    {
        ImportantSection::create(['name' => 'Section B', 'order_number' => 2]);
        ImportantSection::create(['name' => 'Section A', 'order_number' => 1]);

        $response = $this->getJson('/api/link-sections');

        $response->assertJsonPath('data.0.name', 'Section A');
        $response->assertJsonPath('data.1.name', 'Section B');
    }

    public function test_api_links_orders_links_newest_first_within_section(): void
    {
        $section = ImportantSection::create(['name' => 'Kumpulan Link MBKM', 'order_number' => 1]);

        $older = ImportantLink::create(['important_section_id' => $section->id, 'name' => 'Angkatan 2019', 'link' => 'http://bit.ly/MBKM2019']);
        ImportantLink::whereKey($older->id)->update(['created_at' => now()->subDays(10), 'updated_at' => now()->subDays(10)]);

        $newer = ImportantLink::create(['important_section_id' => $section->id, 'name' => 'Angkatan 2020', 'link' => 'http://bit.ly/MBKM2020']);
        ImportantLink::whereKey($newer->id)->update(['created_at' => now()->subDay(), 'updated_at' => now()->subDay()]);

        $response = $this->getJson('/api/link-sections');

        $response->assertJsonPath('data.0.links.0.name', 'Angkatan 2020');
        $response->assertJsonPath('data.0.links.1.name', 'Angkatan 2019');
    }

    public function test_api_links_breaks_link_updated_at_ties_by_id_desc(): void
    {
        $section = ImportantSection::create(['name' => 'Kumpulan Link MBKM', 'order_number' => 1]);

        $first = ImportantLink::create(['important_section_id' => $section->id, 'name' => 'Angkatan 2019', 'link' => 'http://bit.ly/MBKM2019']);
        ImportantLink::whereKey($first->id)->update(['created_at' => now()->subDay(), 'updated_at' => now()->subDay()]);

        $second = ImportantLink::create(['important_section_id' => $section->id, 'name' => 'Angkatan 2020', 'link' => 'http://bit.ly/MBKM2020']);
        ImportantLink::whereKey($second->id)->update(['created_at' => now()->subDay(), 'updated_at' => now()->subDay()]);

        $response = $this->getJson('/api/link-sections');

        $response->assertJsonPath('data.0.links.0.name', 'Angkatan 2020');
        $response->assertJsonPath('data.0.links.1.name', 'Angkatan 2019');
    }

    public function test_api_links_includes_section_without_links(): void
    {
        ImportantSection::create(['name' => 'Kumpulan Link Tugas Akhir', 'order_number' => 1]);

        $response = $this->getJson('/api/link-sections');

        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.links', []);
    }

    public function test_api_links_returns_empty_data_when_no_sections(): void
    {
        $response = $this->getJson('/api/link-sections');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data', []);
    }

    public function test_important_links_returns_flat_newest_first_with_limit(): void
    {
        $section = ImportantSection::create(['name' => 'Layanan', 'order_number' => 1]);
        ImportantLink::create(['important_section_id' => $section->id, 'name' => 'Lama', 'link' => 'https://example.com/lama']);
        ImportantLink::create(['important_section_id' => $section->id, 'name' => 'Baru', 'link' => 'https://example.com/baru']);

        $response = $this->getJson('/api/important-links?limit=2&page=1');

        $response->assertOk();
        $this->assertSame('Baru', $response->json('data.0.name'));
        $this->assertSame(2, $response->json('meta.per_page'));
    }
}