@extends('layouts.adminlayout')

@section('title', 'List Section')

@section('content')
    <div class="admin modern-page">
        <div class="dashboard-heading">
            <h2 class="modern-page__heading">Manajemen Section Link Penting</h2>
            <div class="dashboard-heading__actions">
                <a class="modern-button modern-button--soft" href="{{ route('admin.sections.reorder') }}">
                    <i class="fa-solid fa-sort"></i> Ganti Urutan Section
                </a>
                <a class="modern-button modern-button--primary" href="{{ route('admin.sections.create') }}">
                    <i class="fa-solid fa-plus"></i> Tambah Section
                </a>
                <form method="GET" action="{{ route('admin.sections.index') }}" role="search">
                    <input class="form-control" name="search" type="search" placeholder="Cari"
                        value="{{ request()->get('search') }}" aria-label="Search">
                </form>
            </div>
        </div>
        @include('partials.Alerts')
        <div class="table-admin">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Nama Section</th>
                        <th scope="col" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sections as $section)
                        <tr>
                            <td>{{ $section->name }}</td>
                            <td class="aksi"><a class="edit"
                                    href="{{ route('admin.sections.edit', ['importantSection' => $section->id]) }}" title="Edit" aria-label="Edit"><i class="fa-solid fa-pen"></i></a>
                                <a class="delete" href="#" data-bs-toggle="modal"
                                    data-bs-target="#confirmModal-{{ $section->id }}" title="Hapus" aria-label="Hapus"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($sections->isEmpty())
                @include('partials.Empty')
            @endif
        </div>
        @if ($sections->total() > 0)
            <div class="admin-pagination">{{ $sections->links('pagination::bootstrap-5') }}</div>
        @endif

        @foreach ($sections as $section)
            <div class="modal fade" id="confirmModal-{{ $section->id }}" tabindex="-1"
                aria-labelledby="confirmModalLabel-{{ $section->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel-{{ $section->id }}">Konfirmasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Apakah yakin dihapus?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="modern-button modern-button--soft"
                                data-bs-dismiss="modal">Batal</button>
                            <form id="delete-form-{{ $section->id }}"
                                action="{{ route('admin.sections.destroy', ['importantSection' => $section->id]) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="modern-button modern-button--primary">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection