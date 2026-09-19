@extends('layouts.adminlayout')

@section('title', 'Tinjau Hasil Import')

@section('content')
    <div class="admin modern-page">
        <h2 class="modern-page__heading">Tinjau Hasil Import</h2>

        @include('partials.Alerts')

        @php($staged = $import->payload ?? [])

        @if (empty($staged))
            <div class="empty-state modern-card">
                <i class="fa-regular fa-file-lines"></i>
                <p>Tidak ada dataset pada import ini</p>
                <p>Unggah ulang file Excel untuk memulai import baru.</p>
                <a href="{{ route('admin.dataset-imports.create') }}" class="modern-button modern-button--primary">Upload Ulang</a>
            </div>
        @else
            <form method="POST" action="{{ route('admin.dataset-imports.update', $import) }}">
                @csrf
                @method('PUT')
                <div class="ds-editor-list">
                    @foreach ($staged as $datasetIndex => $dataset)
                        <article class="ds-editor modern-card">
                            <header class="ds-editor__head">
                                <input class="ds-editor__title form-control" type="text" name="datasets[{{ $datasetIndex }}][title]" value="{{ old('datasets.' . $datasetIndex . '.title', $dataset['title'] ?? '') }}" placeholder="Judul chart" aria-label="Judul chart" required>
                                <select class="ds-editor__type form-select" name="datasets[{{ $datasetIndex }}][chart_type]" aria-label="Tipe grafik">
                                    <option value="bar" @selected(old('datasets.' . $datasetIndex . '.chart_type', $dataset['chart_type'] ?? 'bar') === 'bar')>Bar</option>
                                    <option value="line" @selected(old('datasets.' . $datasetIndex . '.chart_type', $dataset['chart_type'] ?? 'bar') === 'line')>Line</option>
                                    <option value="pie" @selected(old('datasets.' . $datasetIndex . '.chart_type', $dataset['chart_type'] ?? 'bar') === 'pie')>Pie</option>
                                </select>
                            </header>
                            <input type="hidden" name="datasets[{{ $datasetIndex }}][sheet_name]" value="{{ old('datasets.' . $datasetIndex . '.sheet_name', $dataset['sheet_name'] ?? '') }}">
                            <input type="hidden" name="datasets[{{ $datasetIndex }}][x_label]" value="{{ old('datasets.' . $datasetIndex . '.x_label', $dataset['x_label'] ?? '') }}">
                            <input type="hidden" name="datasets[{{ $datasetIndex }}][y_label]" value="{{ old('datasets.' . $datasetIndex . '.y_label', $dataset['y_label'] ?? '') }}">
                            <div class="ds-editor__values">
                                @foreach (old('datasets.' . $datasetIndex . '.items', $dataset['items'] ?? []) as $itemIndex => $item)
                                    <div class="ds-row">
                                        <input class="ds-row__label" type="text" name="datasets[{{ $datasetIndex }}][items][{{ $itemIndex }}][label]" value="{{ $item['label'] ?? '' }}" placeholder="Label" aria-label="Label">
                                        <input class="ds-row__value" type="number" step="any" name="datasets[{{ $datasetIndex }}][items][{{ $itemIndex }}][value]" value="{{ $item['value'] ?? '' }}" placeholder="0" aria-label="Nilai">
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="modern-button modern-button--primary"><i class="fa-solid fa-circle-check"></i> Konfirmasi &amp; Simpan</button>
                    <a href="{{ route('admin.dataset-imports.create') }}" class="modern-button modern-button--soft">Upload Ulang</a>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.dataset-imports.destroy', $import) }}" class="mt-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="modern-button modern-button--soft"><i class="fa-solid fa-trash"></i> Batalkan Import</button>
            </form>
        @endif
    </div>
@endsection
