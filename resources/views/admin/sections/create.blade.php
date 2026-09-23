@extends('layouts.admin')

@section('title', 'Tambah Section')

@section('content')
    <div class="admin modern-page">
        <h2 class="modern-page__heading">Form Tambah Section</h2>
        <div class="form row">
            @include('partials.alerts')
            <form method="POST" action="{{ route('admin.sections.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="namasection" class="form-label">
                        <h4>Nama Section<span class="required-star">*</span></h4>
                    </label>
                    <input name="name" type="text" class="form-control" id="namasection" required>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="modern-button modern-button--primary">Simpan</button>
                    <a href="{{ route('admin.sections.index') }}" class="modern-button modern-button--soft">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
