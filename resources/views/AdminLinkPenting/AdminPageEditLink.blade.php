@extends('layouts.adminlayout')

@section('title', 'Edit ' . $link->name)

@section('content')
    <div class="admin modern-page">
        <h2 class="modern-page__heading">Form Pengeditan Link Penting</h2>
        <div class="form row">
            @include('partials.Alerts')
            <form method="POST" action="{{ route('admin.links.update', ['importantLink' => $link->id]) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="tag" class="form-label">
                        <h4>Pilih Section<span class="required-star">*</span></h4>
                    </label>
                    <select class="form-select" aria-label="Default select example" name="section_id">
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}"
                                {{ $section->id == $link->important_section->id ? 'selected' : '' }}>
                                {{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">
                        <h4>Deskripsi<span class="required-star">*</span></h4>
                    </label>
                    <input name="name" type="text" class="form-control" id="deskripsi" value="{{ $link->name }}"
                        required>
                </div>
                <div class="mb-3">
                    <label for="link" class="form-label">
                        <h4>Link<span class="required-star">*</span></h4>
                    </label>
                    <input name="link" type="text" class="form-control" id="link" value="{{ $link->link }}" required>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="modern-button modern-button--primary">Submit</button>
                    <a href="{{ route('admin.links.index') }}" class="modern-button modern-button--soft">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
