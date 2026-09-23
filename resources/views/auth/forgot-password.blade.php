@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi')

@section('content')
    <div class="auth-card">
        <div class="auth-card__body">
            <form method="POST" action="{{ route('admin.password-resets.store') }}">
                @csrf
                <div class="auth-field">
                    <label for="email" class="form-label">Masukkan Email yang Terdaftar</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Masukkan email"
                        required value="{{ old('email') }}">
                </div>
                <div class="auth-actions">
                    <button type="submit" class="btn btn-danger">Kirim</button>
                </div>
            </form>
        </div>
    </div>
@endsection
