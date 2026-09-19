<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DashboardDatasetStoreRequest;
use App\Http\Requests\Admin\DashboardDatasetUpdateRequest;
use App\Models\DashboardDataset;
use App\Models\DashboardDatasetItem;
use App\Models\DatasetImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $dashboardTablesReady = Schema::hasTable('dashboard_datasets')
            && Schema::hasTable('dashboard_dataset_items');

        $datasets = $dashboardTablesReady
            ? DashboardDataset::with(['items' => fn ($query) => $query->orderBy('sort_order')])
                ->orderBy('id')
                ->get()
                ->map(fn (DashboardDataset $dataset) => [
                    'id' => $dataset->id,
                    'title' => $dataset->title,
                    'chart_type' => $dataset->chart_type,
                    'x_label' => $dataset->x_label,
                    'y_label' => $dataset->y_label,
                    'labels' => $dataset->items->pluck('label')->values(),
                    'values' => $dataset->items->pluck('value')->values(),
                ])
            : collect();

        return view('AdminDashboard.index', [
            'datasets' => $datasets,
            'dashboardTablesReady' => $dashboardTablesReady,
        ]);
    }

    public function create(): View
    {
        return view('AdminDashboard.create');
    }

    public function edit(DashboardDataset $dashboardDataset): View
    {
        $dataset = $dashboardDataset->load('items');

        return view('AdminDashboard.edit', ['dataset' => $dataset]);
    }

    public function store(DashboardDatasetStoreRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        DB::transaction(function () use ($validatedData) {
            $usedSlugs = [];

            $created = DashboardDataset::create([
                'title' => $validatedData['title'],
                'slug' => $this->uniqueSlug($validatedData['title'], $usedSlugs),
                'sheet_name' => $validatedData['title'],
                'chart_type' => $validatedData['chart_type'],
                'x_label' => $validatedData['x_label'] ?? '',
                'y_label' => $validatedData['y_label'] ?? '',
                'description' => null,
            ]);

            foreach (array_values($validatedData['items']) as $index => $item) {
                DashboardDatasetItem::create([
                    'dataset_id' => $created->id,
                    'label' => $item['label'],
                    'value' => $item['value'],
                    'sort_order' => $index + 1,
                ]);
            }
        });

        return redirect()->route('admin.datasets.index')->with('success', 'Chart berhasil ditambahkan.');
    }

    public function update(DashboardDatasetUpdateRequest $request, DashboardDataset $dashboardDataset): RedirectResponse
    {
        $dataset = $dashboardDataset;

        $validatedData = $request->validated();

        DB::transaction(function () use ($dataset, $validatedData) {
            $dataset->update([
                'title' => $validatedData['title'],
                'slug' => Str::slug($validatedData['title']) . '-' . $dataset->id,
                'sheet_name' => $validatedData['title'],
                'chart_type' => $validatedData['chart_type'],
                'x_label' => $validatedData['x_label'] ?? $dataset->x_label,
                'y_label' => $validatedData['y_label'] ?? $dataset->y_label,
                'description' => $dataset->description,
            ]);

            $dataset->items()->delete();

            foreach (array_values($validatedData['items']) as $index => $item) {
                DashboardDatasetItem::create([
                    'dataset_id' => $dataset->id,
                    'label' => $item['label'],
                    'value' => $item['value'],
                    'sort_order' => $index + 1,
                ]);
            }
        });

        return redirect()->route('admin.datasets.index')->with('success', 'Chart berhasil diperbarui.');
    }

    public function destroyAll(): RedirectResponse
    {
        DB::transaction(function () {
            DashboardDatasetItem::query()->delete();
            DashboardDataset::query()->delete();
            DatasetImport::query()->delete();
        });

        return redirect()->route('admin.datasets.index')->with('success', 'Semua data dashboard berhasil dihapus.');
    }

    private function uniqueSlug(string $title, array &$usedSlugs): string
    {
        $base = Str::slug($title) ?: 'chart';
        $slug = $base;
        $suffix = 2;

        while (in_array($slug, $usedSlugs, true) || DashboardDataset::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        $usedSlugs[] = $slug;

        return $slug;
    }
}
