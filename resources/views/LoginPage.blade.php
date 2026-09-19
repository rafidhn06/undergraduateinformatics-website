@extends('layouts.authlayout')

@section('title', 'Admin Login')

@section('content')
    <div class="auth-card">
        <div class="auth-card__body" style="padding-top: 32px;">
            <form method="POST" action="{{ route('admin.loginAttempt') }}">
                @csrf
                <div class="auth-field">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email"
                        required>
                </div>
                <div class="auth-field">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Masukkan password" required autocomplete="off">
                </div>
                <div class="auth-actions">
                    <div class="lupa-password">
                        <a href="{{ route('admin.password-resets.create') }}" class="text-decoration-none">Lupa Password?</a>
                    </div>
                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-right-to-bracket"></i> Login</button>
                </div>
            </form>
        </div>
    </div>
@endsection
