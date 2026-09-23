<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ReservationSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'answers' => ['required', 'array', 'min:1', 'max:50'],
            'answers.*.questionId' => ['required', 'string', 'max:255'],
            'answers.*.answer' => ['required', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'answers.required' => 'Jawaban wajib diisi.',
            'answers.min' => 'Minimal satu jawaban wajib diisi.',
            'answers.max' => 'Maksimal 50 jawaban.',
            'answers.*.questionId.max' => 'ID pertanyaan maksimal 255 karakter.',
            'answers.*.answer.max' => 'Jawaban maksimal 5000 karakter.',
        ];
    }
}
