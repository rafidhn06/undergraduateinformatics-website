@extends('layouts.admin')

@section('title', 'Tambah Topik')

@section('content')
    <div class="admin modern-page">
        <h2 class="modern-page__heading">Form Tambah Topik</h2>
        <div class="form row">
            @include('partials.alerts')
            <form method="POST" action="{{ route('admin.tags.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="namatag" class="form-label">
                        <h4>Nama Topik<span class="required-star">*</span></h4>
                    </label>
                    <input name="name" type="text" class="form-control" id="namatag" required>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">
                        <h4>Deskripsi</h4>
                    </label>
                    <textarea name="description" type="text" class="form-control" id="deskripsi" required></textarea>
                </div>
                {{-- <div class="mb-3">
                                <label for="gambar" class="form-label">
                                    <h4>Gambar</h4>
                                </label>
                                <input type="file" accept="image/*" class="form-control" id="gambar">
                            </div> --}}
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="modern-button modern-button--primary">Simpan</button>
                    <a href="{{ route('admin.tags.index') }}" class="modern-button modern-button--soft">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
