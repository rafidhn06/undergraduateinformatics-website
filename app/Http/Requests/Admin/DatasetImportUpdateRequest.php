<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DatasetImportUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'datasets' => ['present', 'array'],
            'datasets.*.title' => ['required', 'string', 'max:255'],
            'datasets.*.chart_type' => ['required', 'in:bar,line,pie'],
            'datasets.*.sheet_name' => ['nullable', 'string', 'max:255'],
            'datasets.*.x_label' => ['nullable', 'string', 'max:255'],
            'datasets.*.y_label' => ['nullable', 'string', 'max:255'],
            'datasets.*.items' => ['required', 'array', 'min:1'],
            'datasets.*.items.*.label' => ['required', 'string', 'max:255'],
            'datasets.*.items.*.value' => ['required', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'datasets.present' => 'Datasets wajib ada.',
            'datasets.array' => 'Datasets harus berupa daftar.',
            'datasets.*.title.required' => 'Judul wajib diisi.',
            'datasets.*.title.string' => 'Judul harus berupa teks.',
            'datasets.*.title.max' => 'Judul maksimal 255 karakter.',
            'datasets.*.chart_type.required' => 'Tipe grafik wajib diisi.',
            'datasets.*.chart_type.in' => 'Tipe grafik harus salah satu dari bar, line, atau pie.',
            'datasets.*.sheet_name.string' => 'Nama sheet harus berupa teks.',
            'datasets.*.sheet_name.max' => 'Nama sheet maksimal 255 karakter.',
            'datasets.*.x_label.string' => 'Label sumbu X harus berupa teks.',
            'datasets.*.x_label.max' => 'Label sumbu X maksimal 255 karakter.',
            'datasets.*.y_label.string' => 'Label sumbu Y harus berupa teks.',
            'datasets.*.y_label.max' => 'Label sumbu Y maksimal 255 karakter.',
            'datasets.*.items.required' => 'Item wajib diisi.',
            'datasets.*.items.array' => 'Item harus berupa daftar.',
            'datasets.*.items.min' => 'Minimal satu item wajib diisi.',
            'datasets.*.items.*.label.required' => 'Label item wajib diisi.',
            'datasets.*.items.*.label.string' => 'Label item harus berupa teks.',
            'datasets.*.items.*.label.max' => 'Label item maksimal 255 karakter.',
            'datasets.*.items.*.value.required' => 'Nilai item wajib diisi.',
            'datasets.*.items.*.value.numeric' => 'Nilai item harus berupa angka.',
        ];
    }
}
