@extends('layouts.authlayout')

@section('title', 'Ganti Password')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="text-center">Lupa Password</h3>
        </div>
        <div class="card-body">
            @include('partials.Alerts')
            <div class="back">
                <a href="{{ Auth::check() ? route('admin.posts.index') : route('admin.login') }}"><i
                        class="fa-solid fa-arrow-left fa-lg"></i>Kembali</a>
            </div>
            <form method="POST" action="{{ route('admin.password-resets.store') }}">
                @csrf
                <div class="email-field">
                    <label for="email" class="form-label">Masukkan Email yang Terdaftar</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email"
                        required>
                </div>
                <div class="bawah d-flex justify-content-end">
                    <button type="submit" class="btn btn-danger">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
