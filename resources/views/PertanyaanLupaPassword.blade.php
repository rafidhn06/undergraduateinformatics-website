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
                <a href="{{ route('admin.password-resets.create') }}"><i class="fa-solid fa-arrow-left fa-lg"></i>Kembali</a>
            </div>
            <form method="POST" action="{{ route('admin.password-resets.update', $reset) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="stage" value="questions">
                <div class="pertanyaan-field">
                    <label for="pertanyaan1" class="form-label">{{ $user->password_recovery->first_question }}</label>
                    <input type="text" class="form-control" name="first_answer" id="pertanyaan1"
                        placeholder="Masukkan Jawaban" required>
                </div>
                <div class="pertanyaan-field">
                    <label for="pertanyaan1" class="form-label">{{ $user->password_recovery->second_question }}</label>
                    <input type="text" class="form-control" name="second_answer" id="pertanyaan2"
                        placeholder="Masukkan Jawaban" required>
                </div>
                <div class="bawah d-flex justify-content-end">
                    <button type="submit" class="btn btn-danger">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
