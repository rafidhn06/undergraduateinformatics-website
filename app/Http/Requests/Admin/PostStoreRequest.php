<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:200000'],
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'tags' => ['required', 'array', 'min:1', 'max:50'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi.',
            'title.string' => 'Judul harus berupa teks.',
            'title.max' => 'Judul maksimal 255 karakter.',
            'subtitle.required' => 'Subjudul wajib diisi.',
            'subtitle.string' => 'Subjudul harus berupa teks.',
            'subtitle.max' => 'Subjudul maksimal 255 karakter.',
            'body.required' => 'Isi wajib diisi.',
            'body.string' => 'Isi harus berupa teks.',
            'body.max' => 'Isi maksimal 200000 karakter.',
            'image.image' => 'Gambar harus berupa berkas gambar.',
            'image.mimes' => 'Gambar harus berformat jpg, png, atau jpeg.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
            'tags.required' => 'Topik wajib diisi.',
            'tags.array' => 'Topik harus berupa daftar.',
            'tags.min' => 'Minimal satu topik wajib dipilih.',
            'tags.max' => 'Maksimal 50 topik.',
            'tags.*.integer' => 'Topik tidak valid.',
            'tags.*.exists' => 'Topik tidak valid.',
        ];
    }
}
