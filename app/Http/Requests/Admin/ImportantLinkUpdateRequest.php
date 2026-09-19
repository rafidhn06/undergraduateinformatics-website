<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportantLinkUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'section_id' => 'required',
            'name' => 'required',
            'link' => 'required | url | active_url',
        ];
    }

    public function messages(): array
    {
        return [
            'section_id.required' => 'Section wajib dipilih.',
            'name.required' => 'Nama wajib diisi.',
            'link.required' => 'Link wajib diisi.',
            'link.url' => 'Link harus berupa URL yang valid.',
            'link.active_url' => 'Link harus berupa URL yang aktif.',
        ];
    }
}
