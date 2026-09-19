@extends('layouts.adminlayout')

@section('title', 'List Link Penting')

@section('content')
    <div class="admin modern-page">
        <div class="dashboard-heading">
            <h2 class="modern-page__heading">Manajemen Link Penting</h2>
            <div class="dashboard-heading__actions">
                <a class="modern-button modern-button--primary" href="{{ route('admin.links.create') }}">
                    <i class="fa-solid fa-plus"></i> Tambah Link Penting
                </a>
                <form method="GET" action="{{ route('admin.links.index') }}" role="search">
                    <input class="form-control" name="search" type="search" placeholder="Cari"
                        value="{{ request()->get('search') }}" aria-label="Search">
                </form>
            </div>
        </div>
        @include('partials.Alerts')
        <div class="table-admin">
            <table class="table table-striped table--links">
                <thead>
                    <tr>
                        <th scope="col">Nama Section</th>
                        <th scope="col">Deskripsi</th>
                        <th scope="col">Link</th>
                        <th scope="col" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($links as $link)
                        <tr>
                            <td>{{ $link->important_section->name }}</td>
                            <td><div class="cell-clamp">{{ $link->name }}</div></td>
                            <td><div class="cell-clamp">{{ $link->link }}</div></td>
                            <td class="aksi"><a class="edit"
                                    href="{{ route('admin.links.edit', ['importantLink' => $link->id]) }}" title="Edit" aria-label="Edit"><i class="fa-solid fa-pen"></i></a>
                                <a class="delete" href="#" data-bs-toggle="modal"
                                    data-bs-target="#confirmModal-{{ $link->id }}" title="Hapus" aria-label="Hapus"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($links->isEmpty())
                @include('partials.Empty')
            @endif
        </div>
        @if ($links->total() > 0)
            <div class="admin-pagination">{{ $links->links('pagination::bootstrap-5') }}</div>
        @endif

        @foreach ($links as $link)
            <div class="modal fade" id="confirmModal-{{ $link->id }}" tabindex="-1"
                aria-labelledby="confirmModalLabel-{{ $link->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel-{{ $link->id }}">
                                Konfirmasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Apakah yakin dihapus?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="modern-button modern-button--soft"
                                data-bs-dismiss="modal">Batal</button>
                            <form id="delete-form-{{ $link->id }}"
                                action="{{ route('admin.links.destroy', ['importantLink' => $link->id]) }}" method="POST">
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