<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRecoveryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_question' => ['required', 'string', 'max:1000'],
            'second_question' => ['required', 'string', 'max:1000'],
            'first_answer' => ['required', 'string', 'max:255'],
            'second_answer' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_question.required' => 'Pertanyaan pertama wajib diisi.',
            'first_question.string' => 'Pertanyaan pertama harus berupa teks.',
            'first_question.max' => 'Pertanyaan pertama maksimal 1000 karakter.',
            'second_question.required' => 'Pertanyaan kedua wajib diisi.',
            'second_question.string' => 'Pertanyaan kedua harus berupa teks.',
            'second_question.max' => 'Pertanyaan kedua maksimal 1000 karakter.',
            'first_answer.required' => 'Jawaban pertama wajib diisi.',
            'first_answer.string' => 'Jawaban pertama harus berupa teks.',
            'first_answer.max' => 'Jawaban pertama maksimal 255 karakter.',
            'second_answer.required' => 'Jawaban kedua wajib diisi.',
            'second_answer.string' => 'Jawaban kedua harus berupa teks.',
            'second_answer.max' => 'Jawaban kedua maksimal 255 karakter.',
        ];
    }
}
