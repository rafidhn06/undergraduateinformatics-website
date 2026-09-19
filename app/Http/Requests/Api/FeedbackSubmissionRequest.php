<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.questionId' => ['required', 'string'],
            'answers.*.answer' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'answers.required' => 'Jawaban wajib diisi.',
            'answers.array' => 'Jawaban harus berupa daftar.',
            'answers.min' => 'Minimal satu jawaban wajib diisi.',
        ];
    }
}
