@extends('layouts.adminlayout')

@section('title', 'Statistik Mahasiswa')

@section('content')
    <div class="admin modern-page">
        <div class="dashboard-heading">
            <h2 class="modern-page__heading">Statistik Mahasiswa</h2>
            <div class="dashboard-heading__actions">
                <a class="modern-button modern-button--soft" href="{{ route('admin.datasets.create') }}">
                    <i class="fa-solid fa-plus"></i> Tambah Chart Manual
                </a>
                <a class="modern-button modern-button--primary" href="{{ route('admin.dataset-imports.create') }}">
                    <i class="fa-solid fa-file-arrow-up"></i> Upload Data Excel
                </a>
            </div>
        </div>

        @include('partials.Alerts')

        @if (! $dashboardTablesReady)
            <div class="empty-state modern-card">
                <i class="fa-solid fa-database"></i>
                <p>Database Statistik Mahasiswa belum siap</p>
                <p>Tabel untuk menyimpan data Statistik Mahasiswa belum dibuat. Setelah migration dashboard dijalankan, gunakan tombol Upload Data Excel di atas.</p>
            </div>
        @elseif ($datasets->isEmpty())
            <div class="empty-state modern-card">
                <i class="fa-regular fa-file-lines"></i>
                <p>Belum ada data yang dipublikasikan</p>
                <p>Upload file Excel atau tambah chart manual untuk menerbitkan data pada Statistik Mahasiswa.</p>
            </div>
        @else
            <div class="chart-grid">
                @foreach ($datasets as $dataset)
                    <article class="chart-card">
                        <div class="chart-card__head">
                            <h3 class="chart-card__title">{{ $dataset['title'] }}</h3>
                            <a class="chart-card__edit" href="{{ route('admin.datasets.edit', ['dashboardDataset' => $dataset['id']]) }}" title="Edit" aria-label="Edit"><i class="fa-solid fa-pen"></i></a>
                        </div>
                        <div class="chart-canvas"><canvas id="chart-{{ $dataset['id'] }}"></canvas></div>
                        @if ($dataset['x_label'])
                            <p class="chart-card__axis">{{ $dataset['x_label'] }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@include('AdminDashboard._chart-assets')

@push('scripts')
    <script>
        const datasets = @json($datasets);
        datasets.forEach((dataset) => {
            const canvas = document.getElementById(`chart-${dataset.id}`);
            if (!canvas) return;
            window.renderChartPreview(
                canvas,
                dataset.labels,
                dataset.values.map((value) => Number(value)),
                ['bar', 'line', 'pie'].includes(dataset.chart_type?.toLowerCase()) ? dataset.chart_type.toLowerCase() : 'bar'
            );
        });
    </script>
@endpush