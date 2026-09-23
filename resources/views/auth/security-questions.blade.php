@extends('layouts.auth')

@section('title', 'Verifikasi Keamanan')

@section('content')
    <div class="auth-card">
        <div class="auth-card__body">
            <form method="POST" action="{{ route('admin.password-resets.update', $reset) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="stage" value="questions">
                <div class="auth-field">
                    <label for="pertanyaan1" class="form-label">{{ $user->passwordRecovery->first_question }}</label>
                    <input type="text" class="form-control" name="first_answer" id="pertanyaan1"
                        placeholder="Masukkan jawaban" required>
                </div>
                <div class="auth-field">
                    <label for="pertanyaan2" class="form-label">{{ $user->passwordRecovery->second_question }}</label>
                    <input type="text" class="form-control" name="second_answer" id="pertanyaan2"
                        placeholder="Masukkan jawaban" required>
                </div>
                <div class="auth-actions">
                    <button type="submit" class="btn btn-danger">Kirim</button>
                </div>
            </form>
        </div>
    </div>
@endsection
