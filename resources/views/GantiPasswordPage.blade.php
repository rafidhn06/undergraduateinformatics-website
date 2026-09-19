@extends('layouts.authlayout')

@section('title', 'Ganti Password')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="text-center">Ganti Password Baru</h3>
        </div>
        <div class="card-body">
            @include('partials.Alerts')
            <div class="back">
                <a href="{{ route('admin.password-resets.create') }}"><i class="fa-solid fa-arrow-left fa-lg"></i>Kembali</a>
            </div>
            <form method="POST" action="{{ route('admin.password-resets.update', $reset) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="stage" value="new_password">
                <div class="password-field">
                    <label for="password" class="form-label">Masukkan Password Baru</label>
                    <input type="password" class="form-control" name="new_password" id="password"
                        placeholder="Enter your password" required minlength="6" autocomplete="off">
                </div>
                <div class="password-field">
                    <label for="password" class="form-label">Masukkan Password Baru Lagi</label>
                    <input type="password" class="form-control" name="new_password_confirmation" id="password"
                        placeholder="Enter your password" required minlength="6" autocomplete="off">
                </div>
                <div class="bawah d-flex justify-content-end">
                    <button type="submit" class="btn btn-danger">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
