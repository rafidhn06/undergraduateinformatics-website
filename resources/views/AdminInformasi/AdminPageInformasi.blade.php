@extends('layouts.adminlayout')

@section('title', 'List Informasi')

@section('content')
    <div class="admin modern-page">
        <div class="dashboard-heading">
            <h2 class="modern-page__heading">Manajemen Informasi</h2>
            <div class="dashboard-heading__actions">
                <a class="modern-button modern-button--primary" href="{{ route('admin.posts.create') }}">
                    <i class="fa-solid fa-plus"></i> Tambah Informasi
                </a>
                <form method="GET" action="{{ route('admin.posts.index') }}" role="search">
                    <input class="form-control" name="search" type="search" placeholder="Cari"
                        value="{{ request()->get('search') }}" aria-label="Search">
                </form>
            </div>
        </div>
        @include('partials.Alerts')
        <div class="table-admin">
            <table class="table table-striped table--posts">
                <thead>
                    <tr>
                        <th scope="col">Judul</th>
                        <th scope="col">Sub-Judul</th>
                        <th scope="col">Deskripsi</th>
                        <th scope="col">Gambar</th>
                        <th scope="col">Tag</th>
                        <th scope="col" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($posts as $data)
                        <tr>
                            <td><div class="cell-clamp">{{ Str::limit($data->title, 100) }}</div></td>
                            <td><div class="cell-clamp">{{ Str::limit($data->subtitle, 100) }}</div></td>
                            <td><div class="cell-clamp">{{ Str::limit(strip_tags($data->body), 120) }}</div></td>
                            <td>@if ($data->hasImage())<img src="{{ asset('storage/' . $data->image) }}" alt="{{ $data->title }}">@endif</td>
                            <td>
                                {{ $data->tags->pluck('name')->join(', ') }}
                            </td>
                            <td class="aksi"><a class="edit"
                                    href="{{ route('admin.posts.edit', ['post' => $data]) }}" title="Edit" aria-label="Edit"><i class="fa-solid fa-pen"></i></a>
                                <a class="delete" href="#" data-bs-toggle="modal"
                                    data-bs-target="#confirmModal-{{ $data->id }}" title="Hapus" aria-label="Hapus"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($posts->isEmpty())
                @include('partials.Empty')
            @endif
        </div>
        @if ($posts->total() > 0)
            <div class="admin-pagination">{{ $posts->links('pagination::bootstrap-5') }}</div>
        @endif

        @foreach ($posts as $data)
            <div class="modal fade" id="confirmModal-{{ $data->id }}" tabindex="-1"
                aria-labelledby="confirmModalLabel-{{ $data->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel-{{ $data->id }}">Konfirmasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Apakah yakin dihapus?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="modern-button modern-button--soft"
                                data-bs-dismiss="modal">Batal</button>
                            <form id="delete-form-{{ $data->id }}"
                                action="{{ route('admin.posts.destroy', ['post' => $data->id]) }}" method="POST">
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