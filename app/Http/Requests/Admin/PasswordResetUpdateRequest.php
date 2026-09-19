<?php

namespace App\Http\Requests\Admin;

use App\Models\PasswordReset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PasswordResetUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stage' => ['required', 'in:questions,new_password'],
            'first_answer' => ['required_if:stage,questions', 'nullable', 'string', 'max:255'],
            'second_answer' => ['required_if:stage,questions', 'nullable', 'string', 'max:255'],
            'new_password' => ['required_if:stage,new_password', 'nullable', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $reset = $this->route('passwordReset');

            if ($reset instanceof PasswordReset && $this->input('stage') !== $reset->stage) {
                $validator->errors()->add('stage', 'Tahapan tidak sesuai.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'stage.required' => 'Tahapan wajib diisi.',
            'stage.in' => 'Tahapan tidak valid.',
            'first_answer.required_if' => 'Jawaban pertama wajib diisi.',
            'first_answer.string' => 'Jawaban pertama harus berupa teks.',
            'first_answer.max' => 'Jawaban pertama maksimal 255 karakter.',
            'second_answer.required_if' => 'Jawaban kedua wajib diisi.',
            'second_answer.string' => 'Jawaban kedua harus berupa teks.',
            'second_answer.max' => 'Jawaban kedua maksimal 255 karakter.',
            'new_password.required_if' => 'Password baru wajib diisi.',
            'new_password.string' => 'Password baru harus berupa teks.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ];
    }
}
