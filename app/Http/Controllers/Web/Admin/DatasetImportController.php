<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DatasetImportStoreRequest;
use App\Http\Requests\Admin\DatasetImportUpdateRequest;
use App\Models\DashboardDataset;
use App\Models\DashboardDatasetItem;
use App\Models\DatasetImport;
use App\Services\Excel\ExcelExtractor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class DatasetImportController extends Controller
{
    public function create(): View
    {
        return view('dataset-imports.create');
    }

    public function store(DatasetImportStoreRequest $request): RedirectResponse
    {
        try {
            $datasets = app(ExcelExtractor::class)->extract($request->file('excel_file'));
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->back()->withInput()->with('error', 'File Excel tidak dapat diproses. Pastikan format file valid.');
        }

        if ($datasets === []) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada dataset yang dapat dibaca dari file tersebut.');
        }

        DatasetImport::query()->where('expires_at', '<', now())->delete();
        $import = DatasetImport::query()->create([
            'user_id' => $request->user()->id,
            'token' => Str::random(48),
            'status' => 'staged',
            'payload' => $datasets,
            'expires_at' => now()->addHour(),
        ]);

        return redirect()->route('admin.dataset-imports.show', $import);
    }

    public function show(DatasetImport $datasetImport): View
    {
        abort_if($datasetImport->expires_at->isPast(), 410);

        return view('dataset-imports.show', ['import' => $datasetImport]);
    }

    public function update(DatasetImportUpdateRequest $request, DatasetImport $datasetImport): RedirectResponse
    {
        abort_if($datasetImport->expires_at->isPast(), 410);
        DB::transaction(function () use ($request) {
            DashboardDatasetItem::query()->delete();
            DashboardDataset::query()->delete();

            foreach ($request->validated()['datasets'] as $dataset) {
                $created = DashboardDataset::query()->create([
                    'title' => $dataset['title'],
                    'slug' => Str::slug($dataset['title']),
                    'sheet_name' => $dataset['sheet_name'] ?? $dataset['title'],
                    'chart_type' => $dataset['chart_type'],
                    'x_label' => $dataset['x_label'] ?? '',
                    'y_label' => $dataset['y_label'] ?? '',
                ]);

                foreach (array_values($dataset['items']) as $index => $item) {
                    DashboardDatasetItem::query()->create([
                        'dataset_id' => $created->id,
                        'label' => $item['label'],
                        'value' => $item['value'],
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        });
        $datasetImport->delete();

        return redirect()->route('admin.datasets.index')->with('success', 'Data dashboard berhasil disimpan.');
    }

    public function destroy(DatasetImport $datasetImport): RedirectResponse
    {
        $datasetImport->delete();

        return redirect()->route('admin.datasets.index')->with('success', 'Import dibatalkan.');
    }
}
