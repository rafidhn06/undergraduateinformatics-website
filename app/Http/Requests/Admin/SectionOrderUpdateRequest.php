<?php

namespace App\Http\Requests\Admin;

use App\Models\ImportantSection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SectionOrderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'order.required' => 'Urutan harus diisi!',
            'order.*.required' => 'Semua urutan harus diisi!',
            'order.*.integer' => 'Urutan harus berupa angka!',
            'order.*.min' => 'Urutan minimal 1!',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $orders = array_values($this->input('order', []));

            if (count($orders) !== count(array_unique($orders))) {
                $validator->errors()->add('order', 'Urutan tidak boleh duplikat!');
            }

            foreach ($orders as $value) {
                if ($value > ImportantSection::count()) {
                    $validator->errors()->add('order', 'Urutan melebihi jumlah data section!');
                    break;
                }
            }
        });
    }
}
