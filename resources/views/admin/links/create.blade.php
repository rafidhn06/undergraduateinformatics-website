@extends('layouts.admin')

@section('title', 'Tambah Tautan Penting')

@section('content')
    <div class="admin modern-page">
        <h2 class="modern-page__heading">Form Tambah Tautan Penting</h2>
        <div class="form row">
            @include('partials.alerts')
            <form method="POST" action="{{ route('admin.links.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="section_id" class="form-label">
                        <h4>Pilih Section<span class="required-star">*</span></h4>
                    </label>
                    <select id="section_id" class="form-select" aria-label="Pilih section" name="section_id">
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">
                        <h4>Deskripsi<span class="required-star">*</span></h4>
                    </label>
                    <input name="name" type="text" class="form-control" id="deskripsi" required>
                </div>
                <div class="mb-3">
                    <label for="link" class="form-label">
                        <h4>Tautan<span class="required-star">*</span></h4>
                    </label>
                    <input name="link" type="text" class="form-control" id="link" required>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="modern-button modern-button--primary">Simpan</button>
                    <a href="{{ route('admin.links.index') }}" class="modern-button modern-button--soft">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
