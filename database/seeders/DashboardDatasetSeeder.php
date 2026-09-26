<?php

namespace Database\Seeders;

use App\Models\DashboardDataset;
use App\Models\DashboardDatasetItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DashboardDatasetSeeder extends Seeder
{
    public function run(): void
    {
        $datasets = [
            [
                'title' => 'Jumlah Mahasiswa per Angkatan',
                'sheet_name' => 'Mahasiswa',
                'chart_type' => 'bar',
                'description' => 'Jumlah mahasiswa aktif per angkatan.',
                'items' => [
                    ['label' => '2022', 'value' => 240],
                    ['label' => '2023', 'value' => 255],
                    ['label' => '2024', 'value' => 270],
                    ['label' => '2025', 'value' => 285],
                ],
            ],
            [
                'title' => 'Mahasiswa per Provinsi Asal',
                'sheet_name' => 'Mahasiswa',
                'chart_type' => 'pie',
                'description' => 'Persebaran mahasiswa berdasarkan provinsi asal.',
                'items' => [
                    ['label' => 'Jawa Barat', 'value' => 350],
                    ['label' => 'Jawa Tengah', 'value' => 180],
                    ['label' => 'Jawa Timur', 'value' => 120],
                    ['label' => 'Banten', 'value' => 90],
                    ['label' => 'Sumatera', 'value' => 60],
                ],
            ],
            [
                'title' => 'Distribusi Mahasiswa per Gender',
                'sheet_name' => 'Mahasiswa',
                'chart_type' => 'pie',
                'description' => 'Proporsi mahasiswa berdasarkan gender.',
                'items' => [
                    ['label' => 'Laki-laki', 'value' => 480],
                    ['label' => 'Perempuan', 'value' => 320],
                ],
            ],
            [
                'title' => 'Mahasiswa Aktif per Bulan',
                'sheet_name' => 'Mahasiswa',
                'chart_type' => 'line',
                'description' => 'Jumlah mahasiswa aktif setiap bulan.',
                'items' => [
                    ['label' => 'Jan', 'value' => 490],
                    ['label' => 'Feb', 'value' => 495],
                    ['label' => 'Mar', 'value' => 500],
                    ['label' => 'Apr', 'value' => 510],
                    ['label' => 'Mei', 'value' => 505],
                    ['label' => 'Jun', 'value' => 520],
                ],
            ],
        ];

        foreach ($datasets as $datasetData) {
            $dataset = DashboardDataset::firstOrCreate(
                ['slug' => Str::slug($datasetData['title'])],
                [
                    'title' => $datasetData['title'],
                    'sheet_name' => $datasetData['sheet_name'],
                    'chart_type' => $datasetData['chart_type'],
                    'description' => $datasetData['description'],
                ]
            );

            foreach ($datasetData['items'] as $index => $itemData) {
                DashboardDatasetItem::firstOrCreate(
                    ['dataset_id' => $dataset->id, 'label' => $itemData['label']],
                    ['value' => $itemData['value'], 'sort_order' => $index + 1]
                );
            }
        }
    }
}
