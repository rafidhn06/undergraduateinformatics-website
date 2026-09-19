<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DatasetImportStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls'],
        ];
    }

    public function messages(): array
    {
        return [
            'excel_file.required' => 'File excel wajib diunggah.',
            'excel_file.file' => 'File excel harus berupa berkas.',
            'excel_file.mimes' => 'File excel harus berformat xlsx atau xls.',
        ];
    }
}
