<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FormLinkUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'feedback_link' => ['nullable', 'url'],
            'reservation_link' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'feedback_link.url' => 'Tautan masukan harus berupa URL yang valid.',
            'reservation_link.url' => 'Tautan reservasi harus berupa URL yang valid.',
        ];
    }
}
