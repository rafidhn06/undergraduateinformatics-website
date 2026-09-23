@extends('layouts.auth')

@section('title', 'Buat Kata Sandi Baru')

@section('content')
    <div class="auth-card">
        <div class="auth-card__body">
            <form method="POST" action="{{ route('admin.password-resets.update', $reset) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="stage" value="new_password">
                <div class="auth-field">
                    <label for="new_password" class="form-label">Kata Sandi Baru</label>
                    <input type="password" class="form-control" name="new_password" id="new_password"
                        placeholder="Masukkan kata sandi baru" required minlength="6" autocomplete="new-password">
                </div>
                <div class="auth-field">
                    <label for="new_password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" class="form-control" name="new_password_confirmation"
                        id="new_password_confirmation" placeholder="Ulangi kata sandi baru" required minlength="6"
                        autocomplete="new-password">
                </div>
                <div class="auth-actions">
                    <button type="submit" class="btn btn-danger">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
