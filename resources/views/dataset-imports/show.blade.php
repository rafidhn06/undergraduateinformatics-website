@extends('layouts.admin')

@section('title', 'Tinjau Hasil Impor')

@section('content')
    <div class="admin modern-page">
        <h2 class="modern-page__heading">Tinjau Hasil Impor</h2>

        @include('partials.alerts')

        @if (empty($staged))
            <div class="empty-state modern-card">
                <i class="fa-regular fa-file-lines"></i>
                <p>Tidak ada dataset pada import ini</p>
                <p>Unggah ulang file Excel untuk memulai impor baru.</p>
                <a href="{{ route('admin.dataset-imports.create') }}" class="modern-button modern-button--primary">Unggah Ulang</a>
            </div>
        @else
            @php($reviewDatasets = old('datasets', $staged))
            <form id="confirm-import-form" method="POST" action="{{ route('admin.dataset-imports.confirm', $import) }}">
                @csrf
                @method('PUT')
                <div class="ds-editor-list">
                    @foreach ($reviewDatasets as $datasetIndex => $dataset)
                        <article class="ds-editor modern-card" data-dataset-index="{{ $datasetIndex }}">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="dataset-title-{{ $datasetIndex }}"><h4>Judul Grafik<span class="required-star">*</span></h4></label>
                                    <input id="dataset-title-{{ $datasetIndex }}" name="datasets[{{ $datasetIndex }}][title]" type="text" class="form-control" value="{{ old('datasets.' . $datasetIndex . '.title', $dataset['title'] ?? '') }}" placeholder="Judul grafik" aria-label="Judul grafik" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="dataset-type-{{ $datasetIndex }}"><h4>Tipe Grafik</h4></label>
                                    <select id="dataset-type-{{ $datasetIndex }}" name="datasets[{{ $datasetIndex }}][chart_type]" class="form-select" aria-label="Tipe grafik">
                                        <option value="bar" @selected(old('datasets.' . $datasetIndex . '.chart_type', $dataset['chart_type'] ?? 'bar') === 'bar')>Bar</option>
                                        <option value="line" @selected(old('datasets.' . $datasetIndex . '.chart_type', $dataset['chart_type'] ?? 'bar') === 'line')>Line</option>
                                        <option value="pie" @selected(old('datasets.' . $datasetIndex . '.chart_type', $dataset['chart_type'] ?? 'bar') === 'pie')>Pie</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="datasets[{{ $datasetIndex }}][sheet_name]" value="{{ old('datasets.' . $datasetIndex . '.sheet_name', $dataset['sheet_name'] ?? '') }}">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <span class="form-label"><h4>Preview</h4></span>
                                    <div class="ds-editor__preview"><canvas id="review-chart-{{ $datasetIndex }}"></canvas></div>
                                </div>
                                <div class="col-md-6">
                                    <span class="form-label"><h4>Data<span class="required-star">*</span></h4></span>
                                    <div class="ds-editor__values">
                                        @foreach (old('datasets.' . $datasetIndex . '.items', $dataset['items'] ?? []) as $itemIndex => $item)
                                            <div class="ds-row">
                                                <input class="ds-row__label" type="text" name="datasets[{{ $datasetIndex }}][items][{{ $itemIndex }}][label]" value="{{ $item['label'] ?? '' }}" placeholder="Label" aria-label="Label">
                                                <input class="ds-row__value" type="number" step="any" name="datasets[{{ $datasetIndex }}][items][{{ $itemIndex }}][value]" value="{{ $item['value'] ?? '' }}" placeholder="0" aria-label="Nilai">
                                                <button type="button" class="ds-row__remove" title="Hapus baris" aria-label="Hapus baris"><i class="fa-solid fa-xmark"></i></button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="ds-editor__add mt-2"><i class="fa-solid fa-plus"></i> Tambah data</button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </form>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" form="confirm-import-form" class="modern-button modern-button--primary"><i class="fa-solid fa-circle-check"></i> Konfirmasi &amp; Simpan</button>
                <a href="{{ route('admin.dataset-imports.create') }}" class="modern-button modern-button--soft">Unggah Ulang</a>
                <form method="POST" action="{{ route('admin.dataset-imports.destroy', $import) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="modern-button modern-button--soft">Batalkan Impor</button>
                </form>
            </div>
        @endif
    </div>
@endsection

@include('admin.dashboard._chart-assets')

@push('scripts')
    <script>
        (function () {
            function refreshArticle(article) {
                const canvas = article.querySelector('canvas[id^="review-chart-"]');
                const typeSelect = article.querySelector('select[name$="[chart_type]"]');
                if (!canvas || !window.renderChartPreview) return;
                const labels = [];
                const values = [];
                article.querySelectorAll('.ds-row').forEach((row) => {
                    const label = row.querySelector('.ds-row__label').value.trim();
                    const value = Number(row.querySelector('.ds-row__value').value);
                    if (label !== '' && !Number.isNaN(value)) {
                        labels.push(label);
                        values.push(value);
                    }
                });
                window.renderChartPreview(canvas, labels, values, typeSelect ? typeSelect.value : 'bar');
            }
            document.querySelectorAll('.ds-editor[data-dataset-index]').forEach((article) => {
                const datasetIndex = article.getAttribute('data-dataset-index');
                const valuesBox = article.querySelector('.ds-editor__values');
                const typeSelect = article.querySelector('select[name$="[chart_type]"]');
                const addButton = article.querySelector('.ds-editor__add');
                const refresh = () => refreshArticle(article);
                const reindex = () => {
                    valuesBox.querySelectorAll('.ds-row').forEach((row, rowIndex) => {
                        row.querySelector('.ds-row__label').name = `datasets[${datasetIndex}][items][${rowIndex}][label]`;
                        row.querySelector('.ds-row__value').name = `datasets[${datasetIndex}][items][${rowIndex}][value]`;
                    });
                };
                addButton.addEventListener('click', () => {
                    const index = valuesBox.querySelectorAll('.ds-row').length;
                    const div = document.createElement('div');
                    div.className = 'ds-row';
                    div.innerHTML = `
                        <input class="ds-row__label" type="text" name="datasets[${datasetIndex}][items][${index}][label]" value="" placeholder="Label" aria-label="Label">
                        <input class="ds-row__value" type="number" step="any" name="datasets[${datasetIndex}][items][${index}][value]" value="" placeholder="0" aria-label="Nilai">
                        <button type="button" class="ds-row__remove" title="Hapus baris" aria-label="Hapus baris"><i class="fa-solid fa-xmark"></i></button>`;
                    valuesBox.appendChild(div);
                    refresh();
                });
                valuesBox.addEventListener('click', (event) => {
                    if (!event.target.closest('.ds-row__remove')) return;
                    event.target.closest('.ds-row').remove();
                    reindex();
                    refresh();
                });
                valuesBox.addEventListener('input', refresh);
                if (typeSelect) typeSelect.addEventListener('change', refresh);
                refresh();
            });
        })();
    </script>
@endpush
