<?php

namespace Tests\Feature\Api;

use App\Models\DashboardDataset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatasetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_datasets_index_returns_seeded_dataset_with_items(): void
    {
        $dataset = DashboardDataset::create([
            'title' => 'Anggaran 2026',
            'slug' => 'anggaran-2026',
            'sheet_name' => 'Sheet1',
            'chart_type' => 'bar',
            'x_label' => 'Bulan',
            'y_label' => 'Jumlah',
        ]);
        $dataset->items()->create(['label' => 'Februari', 'value' => 20, 'sort_order' => 2]);
        $dataset->items()->create(['label' => 'Januari', 'value' => 10, 'sort_order' => 1]);

        $response = $this->getJson('/api/datasets');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => ['id', 'title', 'chart_type', 'x_label', 'y_label', 'labels', 'values'],
            ],
        ]);
        $response->assertJsonPath('data.0.title', 'Anggaran 2026');
        $response->assertJsonPath('data.0.labels', ['Januari', 'Februari']);
        $response->assertJsonPath('data.0.values', [10, 20]);
    }

    public function test_datasets_index_returns_empty_data_when_no_datasets(): void
    {
        $response = $this->getJson('/api/datasets');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data', []);
    }
}
