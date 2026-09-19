@extends('layouts.adminlayout')

@section('title', 'Approval Reservasi')

@section('content')
    <div class="admin modern-page">
        <div class="reservation-heading">
            <div>
                <h2 class="modern-page__heading">Approval Reservasi</h2>
            </div>
            @if ($reservationTableReady && $reservationDetailsReady)
                <div class="reservation-heading__actions">
                    <form method="GET" action="{{ route('admin.reservations.index') }}" class="d-flex" role="search">
                        <input class="form-control" name="search" type="search" placeholder="Cari"
                            value="{{ request()->get('search') }}" aria-label="Search">
                    </form>
                    <a class="modern-button modern-button--primary" href="{{ route('admin.reservations.create') }}"><i class="fa-solid fa-plus"></i> Tambah Reservasi</a>
                </div>
            @endif
        </div>

        @if (! $reservationTableReady)
            <section class="empty-state modern-card">
                <i class="fa-solid fa-database"></i>
                <p>Database reservasi belum siap</p>
                <p>Tabel <code>reservation_schedules</code> belum tersedia. Jalankan migration reservasi agar pengajuan dapat ditampilkan.</p>
            </section>
        @elseif (! $reservationDetailsReady)
            <section class="empty-state modern-card">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <p>Struktur reservasi belum lengkap</p>
                <p>Kolom detail reservasi belum tersedia. Jalankan migration detail reservasi terlebih dahulu.</p>
            </section>
        @elseif ($reservations->isEmpty())
            <section class="empty-state modern-card">
                <i class="fa-regular fa-calendar-xmark"></i>
                <p>{{ request()->get('search') ? 'Tidak ada hasil untuk pencarian tersebut' : 'Belum ada pengajuan reservasi' }}</p>
                <p>{{ request()->get('search') ? 'Coba gunakan kata kunci lain.' : 'Pengajuan jadwal baru dari formulir reservasi akan muncul di halaman ini.' }}</p>
            </section>
        @else
            @include('partials.Alerts')
            <div class="table-admin">
                <table class="table table-striped table--reservation">
                    <thead>
                        <tr><th>Tanggal</th><th>Sesi</th><th>Diajukan oleh</th><th>Ruangan</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($reservations as $reservation)
                            <tr>
                                <td><strong>{{ \Illuminate\Support\Carbon::parse($reservation->date)->translatedFormat('d M Y') }}</strong><br><small class="text-muted">{{ \Illuminate\Support\Carbon::parse($reservation->date)->translatedFormat('l') }}</small></td>
                                <td>{{ substr($reservation->shift, 0, 5) }} WIB</td>
                                <td><strong>{{ $reservation->requested_by }}</strong>@if ($reservation->study_program)<br><small class="text-muted">{{ $reservation->study_program }}</small>@endif</td>
                                <td>{{ $reservation->meeting_room ?: '—' }}</td>
                                <td class="aksi">
                                    @if ($reservation->document_link)<a class="edit" href="{{ $reservation->document_link }}" target="_blank" rel="noopener noreferrer" title="Lihat PDF" aria-label="Lihat PDF"><i class="fa-regular fa-file-pdf"></i></a>@endif
                                    <a class="edit" href="{{ route('admin.reservations.edit', $reservation) }}" title="Edit" aria-label="Edit"><i class="fa-solid fa-pen"></i></a>
                                    <a class="delete" href="#" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $reservation->id }}" title="Hapus" aria-label="Hapus"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if (method_exists($reservations, 'total') && $reservations->total() > 0)
                <div class="admin-pagination">{{ $reservations->links('pagination::bootstrap-5') }}</div>
            @endif

            @foreach ($reservations as $reservation)
                <div class="modal fade" id="confirmModal-{{ $reservation->id }}" tabindex="-1" aria-labelledby="confirmModalLabel-{{ $reservation->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmModalLabel-{{ $reservation->id }}">Konfirmasi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Apakah yakin menghapus reservasi ini beserta dokumen PDF-nya?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="modern-button modern-button--soft" data-bs-dismiss="modal">Batal</button>
                                <form id="delete-form-{{ $reservation->id }}" action="{{ route('admin.reservations.destroy', $reservation) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="modern-button modern-button--primary">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection