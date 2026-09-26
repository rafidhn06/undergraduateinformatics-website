@extends('layouts.admin')

@section('title', 'Manajemen Tautan Form')

@section('content')
    <div class="admin modern-page">
        <h2 class="modern-page__heading">Manajemen Tautan Form</h2>
        @include('partials.alerts')
        <section class="modern-card">
            <form method="POST" action="{{ route('admin.form-links.update') }}">
                @csrf
                @method('PUT')
                <label for="feedback_link" class="form-label">Tautan Masukan</label>
                <div class="feedback-form-row">
                    <input id="feedback_link" name="feedback_link" type="url" class="form-control @error('feedback_link') is-invalid @enderror"
                        value="{{ old('feedback_link', $feedbackLink?->link) }}" placeholder="https://forms.office.com/..." required>
                    <button class="modern-button modern-button--primary" type="submit" data-save-label="Simpan" data-refresh-label="Muat Ulang"><i class="fa-solid fa-rotate-right"></i> <span>Muat Ulang</span></button>
                </div>
                @error('feedback_link')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="text-muted d-block mt-2">Terakhir dimuat ulang: {{ $feedbackDefinition?->fetched_at?->timezone('Asia/Jakarta')->format('d M Y H:i') ?? 'belum pernah' }}</small>
            </form>
        </section>

        <section class="modern-card">
            <form method="POST" action="{{ route('admin.form-links.update') }}">
                @csrf
                @method('PUT')
                <label for="reservation_link" class="form-label">Tautan Reservasi</label>
                <div class="feedback-form-row">
                    <input id="reservation_link" name="reservation_link" type="url" class="form-control @error('reservation_link') is-invalid @enderror"
                        value="{{ old('reservation_link', $reservationLink?->link) }}" placeholder="https://forms.office.com/..." required>
                    <button class="modern-button modern-button--primary" type="submit" data-save-label="Simpan" data-refresh-label="Muat Ulang"><i class="fa-solid fa-rotate-right"></i> <span>Muat Ulang</span></button>
                </div>
                @error('reservation_link')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </form>
            <small class="text-muted d-block mt-2">Terakhir dimuat ulang: {{ $reservationDefinition?->fetched_at?->timezone('Asia/Jakarta')->format('d M Y H:i') ?? 'belum pernah' }}</small>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const saveIcon = 'fa-solid fa-circle-check';
            const refreshIcon = 'fa-solid fa-rotate-right';

            document.querySelectorAll('form[action="{{ route('admin.form-links.update') }}"]').forEach((form) => {
                const input = form.querySelector('input[type="url"]');
                const button = form.querySelector('button[type="submit"]');
                const icon = button.querySelector('i');
                const label = button.querySelector('span');
                const initialLink = input.value.trim();

                function updateButton() {
                    const isChanged = input.value.trim() !== initialLink;
                    label.textContent = isChanged ? button.dataset.saveLabel : button.dataset.refreshLabel;
                    icon.className = isChanged ? saveIcon : refreshIcon;
                }

                input.addEventListener('input', updateButton);
                updateButton();
            });
        })();
    </script>
@endpush
