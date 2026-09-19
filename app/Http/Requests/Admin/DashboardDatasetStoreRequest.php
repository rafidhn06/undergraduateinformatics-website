<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DashboardDatasetStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'chart_type' => ['required', 'in:bar,line,pie'],
            'x_label' => ['nullable', 'string', 'max:255'],
            'y_label' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.label' => ['required', 'string', 'max:255'],
            'items.*.value' => ['required', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi.',
            'title.string' => 'Judul harus berupa teks.',
            'title.max' => 'Judul maksimal 255 karakter.',
            'chart_type.required' => 'Tipe grafik wajib diisi.',
            'chart_type.in' => 'Tipe grafik harus salah satu dari bar, line, atau pie.',
            'x_label.string' => 'Label sumbu X harus berupa teks.',
            'x_label.max' => 'Label sumbu X maksimal 255 karakter.',
            'y_label.string' => 'Label sumbu Y harus berupa teks.',
            'y_label.max' => 'Label sumbu Y maksimal 255 karakter.',
            'items.required' => 'Item wajib diisi.',
            'items.array' => 'Item harus berupa daftar.',
            'items.min' => 'Minimal satu item wajib diisi.',
            'items.*.label.required' => 'Label item wajib diisi.',
            'items.*.label.string' => 'Label item harus berupa teks.',
            'items.*.label.max' => 'Label item maksimal 255 karakter.',
            'items.*.value.required' => 'Nilai item wajib diisi.',
            'items.*.value.numeric' => 'Nilai item harus berupa angka.',
        ];
    }
}
