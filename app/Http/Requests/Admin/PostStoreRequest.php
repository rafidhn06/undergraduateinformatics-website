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
            'title' => 'required',
            'subtitle' => 'required',
            'body' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,svg|max:2048',
            'tags' => 'required|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi.',
            'subtitle.required' => 'Subjudul wajib diisi.',
            'body.required' => 'Isi wajib diisi.',
            'image.image' => 'Gambar harus berupa berkas gambar.',
            'image.mimes' => 'Gambar harus berformat jpg, png, jpeg, atau svg.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
            'tags.required' => 'Tag wajib diisi.',
            'tags.min' => 'Minimal satu tag wajib dipilih.',
        ];
    }
}
