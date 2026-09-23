@extends('layouts.auth')

@section('title', 'Masuk Admin')

@section('content')
    <div class="auth-card">
        <div class="auth-card__body">
            <form method="POST" action="{{ route('admin.loginAttempt') }}">
                @csrf
                <div class="auth-field">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email"
                        required>
                </div>
                <div class="auth-field">
                    <div class="auth-field__row">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <a href="{{ route('admin.password-resets.create') }}" class="auth-field__link">Lupa kata sandi?</a>
                    </div>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Masukkan kata sandi" required autocomplete="off">
                </div>
                <div class="auth-actions">
                    <button type="submit" class="btn btn-danger">Masuk</button>
                </div>
            </form>
        </div>
    </div>
@endsection
