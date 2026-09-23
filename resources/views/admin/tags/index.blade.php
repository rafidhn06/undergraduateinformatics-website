@extends('layouts.admin')

@section('title', 'Topik Informasi')

@section('content')
    <div class="admin modern-page">
        <div class="dashboard-heading">
            <h2 class="modern-page__heading">Manajemen Topik Informasi</h2>
            <div class="dashboard-heading__actions">
                <a class="modern-button modern-button--primary" href="{{ route('admin.tags.create') }}">
                    <i class="fa-solid fa-plus"></i> Tambah Topik
                </a>
                <form method="GET" action="{{ route('admin.tags.index') }}" role="search">
                    <input class="form-control" name="search" type="search" placeholder="Cari"
                        value="{{ request()->get('search') }}" aria-label="Pencarian">
                </form>
            </div>
        </div>
        @include('partials.alerts')
        <div class="table-admin">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Nama Topik</th>
                        <th scope="col">Deskripsi</th>
                        {{-- <th scope="col">Gambar</th> --}}
                        <th scope="col" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tags as $tag)
                        <tr>
                            <td>{{ $tag->name }}</td>
                            <td><div class="cell-clamp">{{ Str::limit($tag->description, 100) }}</div></td>
                            {{-- <td><img src="/images/imgCard.svg" alt=""></td> --}}
                            <td class="aksi">
                                <a class="edit" href="{{ route('admin.tags.edit', ['tag' => $tag->id]) }}" title="Ubah" aria-label="Ubah"><i class="fa-solid fa-pen"></i></a>
                                <a class="delete" href="#" data-bs-toggle="modal"
                                    data-bs-target="#confirmModal-{{ $tag->id }}" title="Hapus" aria-label="Hapus"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($tags->isEmpty())
                @include('partials.empty')
            @endif
        </div>
        @if ($tags->total() > 0)
            <div class="admin-pagination">{{ $tags->links('pagination::bootstrap-5') }}</div>
        @endif

        @foreach ($tags as $tag)
            <div class="modal fade" id="confirmModal-{{ $tag->id }}" tabindex="-1"
                aria-labelledby="confirmModalLabel-{{ $tag->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel-{{ $tag->id }}">Konfirmasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            Apakah yakin dihapus?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="modern-button modern-button--soft"
                                data-bs-dismiss="modal">Batal</button>
                            <form id="delete-form-{{ $tag->id }}"
                                action="{{ route('admin.tags.destroy', ['tag' => $tag->id]) }}" method="POST">
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