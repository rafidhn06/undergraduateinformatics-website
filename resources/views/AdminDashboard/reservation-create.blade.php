@extends('layouts.adminlayout')

@section('title', 'Tambah Reservasi')

@section('content')
    <div class="admin modern-page">
        <h2 class="modern-page__heading">Form Penambahan Reservasi</h2>

        <div class="form row form--wide">
            @include('partials.Alerts')
            <div class="modern-notice modern-notice--info mb-4">Jadwal hanya dapat dibuat untuk hari Senin, Selasa, Kamis, atau Jumat. Sistem akan menolak sesi yang sudah terisi.</div>
            <form method="POST" action="{{ route('admin.reservations.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label"><h4>Tanggal<span class="required-star">*</span></h4></label>
                        <input name="date" type="date" class="form-control" value="{{ old('date') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Sesi<span class="required-star">*</span></h4></label>
                        <select name="shift" class="form-select" required>
                            <option value="" selected disabled>Pilih sesi</option>
                            <option value="09:00" @selected(old('shift') === '09:00')>09:00 WIB</option>
                            <option value="13:00" @selected(old('shift') === '13:00')>13:00 WIB</option>
                            <option value="15:00" @selected(old('shift') === '15:00')>15:00 WIB</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Diajukan oleh<span class="required-star">*</span></h4></label>
                        <input name="requested_by" class="form-control" value="{{ old('requested_by') }}" placeholder="Nama pemohon" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Ruang pertemuan</h4></label>
                        <input name="meeting_room" class="form-control" value="{{ old('meeting_room') }}" placeholder="Contoh: Ruang Rapat 1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Program studi</h4></label>
                        <input name="study_program" class="form-control" value="{{ old('study_program', 'S1 Informatika') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Peserta</h4></label>
                        <input name="participants" class="form-control" value="{{ old('participants') }}" placeholder="Contoh: 10 orang">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Kota</h4></label>
                        <input name="city" class="form-control" value="{{ old('city') }}" placeholder="Contoh: Bandung">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Nama penandatangan Prodi</h4></label>
                        <input name="prodi_signature_name" class="form-control" value="{{ old('prodi_signature_name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Jabatan penandatangan Prodi</h4></label>
                        <input name="prodi_signature_position" class="form-control" value="{{ old('prodi_signature_position') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Nama penandatangan pihak terkait</h4></label>
                        <input name="related_party_signature_name" class="form-control" value="{{ old('related_party_signature_name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Jabatan penandatangan pihak terkait</h4></label>
                        <input name="related_party_signature_position" class="form-control" value="{{ old('related_party_signature_position') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Agenda</h4></label>
                        <input name="agenda" class="form-control" value="{{ old('agenda') }}" placeholder="Jelaskan tujuan pertemuan">
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="modern-button modern-button--primary"><i class="fa-solid fa-calendar-plus"></i> Simpan Reservasi</button>
                    <a href="{{ route('admin.reservations.index') }}" class="modern-button modern-button--soft">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection