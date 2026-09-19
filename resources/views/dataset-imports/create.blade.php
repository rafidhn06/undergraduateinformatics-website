@extends('layouts.adminlayout')

@section('title', 'Upload Data Statistik Mahasiswa')

@section('content')
    <div class="admin modern-page">
        <h2 class="modern-page__heading">Upload Data Statistik Mahasiswa</h2>

        <div class="form row form--wide">
            @include('partials.Alerts')
            <form method="POST" action="{{ route('admin.dataset-imports.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="excel_file"><h4>Pilih file Excel (.xlsx, .xls)<span class="required-star">*</span></h4></label>
                    <input id="excel_file" name="excel_file" class="form-control" type="file" accept=".xlsx,.xls" required>
                </div>
                <p class="text-muted">File diproses di server. Tinjau hasil ekstraksi pada langkah berikutnya sebelum menyimpan.</p>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="modern-button modern-button--primary"><i class="fa-solid fa-file-arrow-up"></i> Proses File</button>
                    <a href="{{ route('admin.datasets.index') }}" class="modern-button modern-button--soft">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
