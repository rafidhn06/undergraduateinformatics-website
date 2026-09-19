@extends('layouts.adminlayout')

@section('title', 'Edit Pertanyaan')

@section('content')
    <div class="admin modern-page">
        <h2 class="modern-page__heading">Form Pengeditan Pertanyaan untuk Lupa Password</h2>
        <div class="form row">
            @include('partials.Alerts')
            <form method="POST" action="{{ route('admin.password-recovery.update') }}" class="d-flex">
                @csrf
                @method('PUT')
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="link" class="form-label">
                            <h4>Pertanyaan 1</h4>
                        </label>
                        <textarea type="text" name="first_question" class="form-control" id="link" required>{{ $first_question }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="link" class="form-label">
                            <h4>Pertanyaan 2</h4>
                        </label>
                        <textarea type="text" name="second_question" class="form-control" id="link" required>{{ $second_question }}</textarea>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="modern-button modern-button--primary">Submit</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="link" class="form-label">
                            <h4>Jawaban 1</h4>
                        </label>
                        <textarea type="text" name="first_answer" class="form-control" id="link" required>{{ $first_answer }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="link" class="form-label">
                            <h4>Jawaban 2</h4>
                        </label>
                        <textarea type="text" name="second_answer" class="form-control" id="link" required>{{ $second_answer }}</textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
