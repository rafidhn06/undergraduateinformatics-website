@extends('layouts.admin')

@section('title', 'Tambah Grafik')

@section('content')
    <div class="admin modern-page">
        <h2 class="modern-page__heading">Form Tambah Grafik</h2>

        <div class="form row form--wide">
            @include('partials.alerts')
            <form method="POST" action="{{ route('admin.datasets.store') }}">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="title"><h4>Judul Grafik<span class="required-star">*</span></h4></label>
                        <input id="title" name="title" class="form-control" value="{{ old('title') }}" placeholder="Contoh: Jumlah Mahasiswa per Tahun" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="chart_type"><h4>Tipe Grafik</h4></label>
                        <select id="chart_type" name="chart_type" class="form-select">
                            <option value="bar" @selected(old('chart_type', 'bar') === 'bar')>Bar</option>
                            <option value="line" @selected(old('chart_type') === 'line')>Line</option>
                            <option value="pie" @selected(old('chart_type') === 'pie')>Pie</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <span class="form-label"><h4>Preview</h4></span>
                        <div class="ds-editor__preview"><canvas id="chart-preview"></canvas></div>
                    </div>
                    <div class="col-md-6">
                        <span class="form-label"><h4>Data<span class="required-star">*</span></h4></span>
                        <div id="chart-data-rows" class="ds-editor__values">
                            @foreach ($rows as $index => $row)
                                <div class="ds-row">
                                    <input class="ds-row__label" type="text" name="items[{{ $index }}][label]" value="{{ $row['label'] ?? '' }}" placeholder="Label" aria-label="Label">
                                    <input class="ds-row__value" type="number" step="any" name="items[{{ $index }}][value]" value="{{ $row['value'] ?? '' }}" placeholder="0" aria-label="Nilai">
                                    <button type="button" class="ds-row__remove" title="Hapus baris" aria-label="Hapus baris"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="add-data-row" class="ds-editor__add mt-2"><i class="fa-solid fa-plus"></i> Tambah data</button>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="modern-button modern-button--primary">Simpan Grafik</button>
                    <a href="{{ route('admin.datasets.index') }}" class="modern-button modern-button--soft">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('admin.dashboard._chart-assets')

@push('scripts')
    <script>
        (function () {
            const rows = document.getElementById('chart-data-rows');
            const preview = document.getElementById('chart-preview');
            const addBtn = document.getElementById('add-data-row');
            const typeSelect = document.querySelector('[name="chart_type"]');

            function collectRows() {
                const items = [];
                rows.querySelectorAll('.ds-row').forEach((row) => {
                    const label = row.querySelector('.ds-row__label').value.trim();
                    const value = Number(row.querySelector('.ds-row__value').value);
                    if (label !== '' && !Number.isNaN(value)) items.push({ label, value });
                });
                return items;
            }

            function refreshPreview() {
                const items = collectRows();
                window.renderChartPreview(preview, items.map((item) => item.label), items.map((item) => item.value), typeSelect.value);
            }

            function reindex() {
                rows.querySelectorAll('.ds-row').forEach((row, index) => {
                    row.querySelector('.ds-row__label').name = `items[${index}][label]`;
                    row.querySelector('.ds-row__value').name = `items[${index}][value]`;
                });
            }

            addBtn.addEventListener('click', () => {
                const index = rows.querySelectorAll('.ds-row').length;
                const div = document.createElement('div');
                div.className = 'ds-row';
                div.innerHTML = `
                    <input class="ds-row__label" type="text" name="items[${index}][label]" value="" placeholder="Label" aria-label="Label">
                    <input class="ds-row__value" type="number" step="any" name="items[${index}][value]" value="" placeholder="0" aria-label="Nilai">
                    <button type="button" class="ds-row__remove" title="Hapus baris" aria-label="Hapus baris"><i class="fa-solid fa-xmark"></i></button>`;
                rows.appendChild(div);
                refreshPreview();
            });

            rows.addEventListener('click', (event) => {
                if (!event.target.closest('.ds-row__remove')) return;
                event.target.closest('.ds-row').remove();
                reindex();
                refreshPreview();
            });

            rows.addEventListener('input', refreshPreview);
            typeSelect.addEventListener('change', refreshPreview);
            refreshPreview();
        })();
    </script>
@endpush