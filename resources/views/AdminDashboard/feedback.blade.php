@extends('layouts.adminlayout')

@section('title', 'Manajemen Form Link')

@section('content')
    <div class="admin modern-page">
        <h2 class="modern-page__heading">Manajemen Form Link</h2>
        @include('partials.Alerts')
        <section class="modern-card">
            <form method="POST" action="{{ route('admin.form-link.feedback.update') }}">
                @csrf
                @method('PUT')
                <label for="feedback_link" class="form-label">Tautan Masukan</label>
                <div class="feedback-form-row">
                    <input id="feedback_link" name="feedback_link" type="url" class="form-control @error('feedback_link') is-invalid @enderror"
                        value="{{ old('feedback_link', $feedbackLink?->link) }}" placeholder="https://forms.office.com/..." required>
                    <button class="modern-button modern-button--primary" type="submit"><i class="fa-solid fa-circle-check"></i> Simpan</button>
                </div>
                @error('feedback_link')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </form>
            <small class="text-muted d-block mt-2">Terakhir di-refresh: {{ $feedbackDefinition?->fetched_at?->format('d M Y H:i') ?? 'belum pernah' }}</small>
            <form method="POST" action="{{ route('admin.form-link.refresh', 'feedback') }}" class="mt-2">
                @csrf
                @method('PUT')
                <button class="modern-button modern-button--soft" type="submit"><i class="fa-solid fa-rotate-right"></i> Refresh</button>
            </form>
        </section>

        <section class="modern-card">
            <form method="POST" action="{{ route('admin.form-link.reservation.update') }}">
                @csrf
                @method('PUT')
                <label for="reservation_link" class="form-label">Tautan Reservasi</label>
                <div class="feedback-form-row">
                    <input id="reservation_link" name="reservation_link" type="url" class="form-control @error('reservation_link') is-invalid @enderror"
                        value="{{ old('reservation_link', $reservationLink?->link) }}" placeholder="https://forms.office.com/..." required>
                    <button class="modern-button modern-button--primary" type="submit"><i class="fa-solid fa-circle-check"></i> Simpan</button>
                </div>
                @error('reservation_link')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </form>
            <small class="text-muted d-block mt-2">Terakhir di-refresh: {{ $reservationDefinition?->fetched_at?->format('d M Y H:i') ?? 'belum pernah' }}</small>
            <form method="POST" action="{{ route('admin.form-link.refresh', 'reservation') }}" class="mt-2">
                @csrf
                @method('PUT')
                <button class="modern-button modern-button--soft" type="submit"><i class="fa-solid fa-rotate-right"></i> Refresh</button>
            </form>
        </section>
    </div>
@endsection
