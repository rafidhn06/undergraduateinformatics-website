@extends('layouts.adminlayout')

@section('title', 'Edit Reservasi')

@section('content')
    <div class="admin modern-page">
        <h2 class="modern-page__heading">Form Pengeditan Reservasi</h2>

        <div class="form row form--wide">
            @include('partials.Alerts')
            <div class="modern-notice modern-notice--info mb-4">Menyimpan perubahan akan memperbarui data dan membuat ulang berita acara PDF.</div>
            <form method="POST" action="{{ route('admin.reservation.update', ['id' => $reservation->id]) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label"><h4>Tanggal<span class="required-star">*</span></h4></label>
                        <input name="date" type="date" class="form-control" value="{{ old('date', substr($reservation->date, 0, 10)) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Sesi<span class="required-star">*</span></h4></label>
                        <select name="shift" class="form-select" required>
                            <option value="09:00" @selected(substr($reservation->shift, 0, 5) === '09:00')>09:00 WIB</option>
                            <option value="13:00" @selected(substr($reservation->shift, 0, 5) === '13:00')>13:00 WIB</option>
                            <option value="15:00" @selected(substr($reservation->shift, 0, 5) === '15:00')>15:00 WIB</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Diajukan oleh<span class="required-star">*</span></h4></label>
                        <input name="requested_by" class="form-control" value="{{ old('requested_by', $reservation->requested_by) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Ruang pertemuan</h4></label>
                        <input name="meeting_room" class="form-control" value="{{ old('meeting_room', $reservation->meeting_room) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Program studi</h4></label>
                        <input name="study_program" class="form-control" value="{{ old('study_program', $reservation->study_program) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Peserta</h4></label>
                        <input name="participants" class="form-control" value="{{ old('participants', $reservation->participants) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Kota</h4></label>
                        <input name="city" class="form-control" value="{{ old('city', $reservation->city) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Nama penandatangan Prodi</h4></label>
                        <input name="prodi_signature_name" class="form-control" value="{{ old('prodi_signature_name', $reservation->prodi_signature_name) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Jabatan penandatangan Prodi</h4></label>
                        <input name="prodi_signature_position" class="form-control" value="{{ old('prodi_signature_position', $reservation->prodi_signature_position) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Nama penandatangan pihak terkait</h4></label>
                        <input name="related_party_signature_name" class="form-control" value="{{ old('related_party_signature_name', $reservation->related_party_signature_name) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Jabatan penandatangan pihak terkait</h4></label>
                        <input name="related_party_signature_position" class="form-control" value="{{ old('related_party_signature_position', $reservation->related_party_signature_position) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><h4>Agenda</h4></label>
                        <input name="agenda" class="form-control" value="{{ old('agenda', $reservation->agenda) }}" placeholder="Jelaskan tujuan pertemuan">
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="modern-button modern-button--primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                    <a href="{{ route('admin.reservation') }}" class="modern-button modern-button--soft">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection