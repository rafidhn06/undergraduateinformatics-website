<?php

namespace Tests\Feature;

use App\Http\Resources\DashboardDatasetResource;
use App\Models\DashboardDataset;
use App\Models\DashboardDatasetItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardDatasetResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_resource_maps_dataset_with_ordered_items(): void
    {
        $dataset = DashboardDataset::create([
            'title' => 'Jumlah Mahasiswa',
            'slug' => 'jumlah-mahasiswa',
            'sheet_name' => 'Sheet1',
            'chart_type' => 'bar',
            'description' => null,
        ]);
        DashboardDatasetItem::create(['dataset_id' => $dataset->id, 'label' => '2024', 'value' => 120, 'sort_order' => 2]);
        DashboardDatasetItem::create(['dataset_id' => $dataset->id, 'label' => '2023', 'value' => 100, 'sort_order' => 1]);

        $resolved = DashboardDatasetResource::collection([$dataset->load(['items' => fn ($query) => $query->orderBy('sort_order')])])->resolve();

        $this->assertSame([
            'id' => $dataset->id,
            'title' => 'Jumlah Mahasiswa',
            'chart_type' => 'bar',
            'labels' => ['2023', '2024'],
            'values' => [100.0, 120.0],
        ], $resolved[0]);
    }

    public function test_resource_handles_dataset_without_items(): void
    {
        $dataset = DashboardDataset::create([
            'title' => 'Kosong',
            'slug' => 'kosong',
            'sheet_name' => 'Sheet1',
            'chart_type' => 'pie',
            'description' => null,
        ]);

        $resolved = DashboardDatasetResource::collection([$dataset->load(['items' => fn ($query) => $query->orderBy('sort_order')])])->resolve();

        $this->assertSame([], $resolved[0]['labels']);
        $this->assertSame([], $resolved[0]['values']);
    }
}
