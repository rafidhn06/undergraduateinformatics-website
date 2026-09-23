<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DashboardDatasetStoreRequest;
use App\Http\Requests\Admin\DashboardDatasetUpdateRequest;
use App\Http\Resources\DashboardDatasetResource;
use App\Models\DashboardDataset;
use App\Models\DashboardDatasetItem;
use App\Models\DatasetImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardDatasetController extends Controller
{
    public function index(): View
    {
        $dashboardTablesReady = Schema::hasTable('dashboard_datasets')
            && Schema::hasTable('dashboard_dataset_items');

        $datasets = $dashboardTablesReady
            ? collect(DashboardDatasetResource::collection(
                DashboardDataset::with(['items' => fn ($query) => $query->orderBy('sort_order')])
                    ->orderBy('id')
                    ->get()
            )->resolve())
            : collect();

        return view('admin.dashboard.index', [
            'datasets' => $datasets,
            'dashboardTablesReady' => $dashboardTablesReady,
        ]);
    }

    public function create(): View
    {
        return view('admin.dashboard.create', [
            'rows' => old('items', [['label' => '', 'value' => '']]),
        ]);
    }

    public function edit(DashboardDataset $dashboardDataset): View
    {
        $dataset = $dashboardDataset->load('items');

        return view('admin.dashboard.edit', [
            'dataset' => $dataset,
            'rows' => old('items', $dataset->items->map(fn ($item) => ['label' => $item->label, 'value' => $item->value])->all()),
        ]);
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

        return redirect()->route('admin.datasets.index')->with('success', 'Grafik berhasil ditambahkan.');
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

        return redirect()->route('admin.datasets.index')->with('success', 'Grafik berhasil diperbarui.');
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
