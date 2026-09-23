<?php

namespace App\Http\Requests\Admin;

use App\Services\Reservation\ReservationTimetable;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ReservationScheduleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $day = Carbon::parse($value)->dayOfWeekIso;
                    if (! in_array($day, ReservationTimetable::allowedDays())) {
                        $fail('Tanggal harus hari Senin, Selasa, Kamis, atau Jumat.');
                    }
                },
            ],
            'shift' => ['required', 'in:' . implode(',', ReservationTimetable::acceptedShiftInputs())],
            'requested_by' => 'required|string|max:255',
            'document_link' => 'nullable|string|url|max:255',
            'meeting_room' => 'nullable|string|max:255',
            'study_program' => 'nullable|string|max:255',
            'participants' => 'nullable|string|max:255',
            'agenda' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'prodi_signature_name' => 'nullable|string|max:255',
            'prodi_signature_position' => 'nullable|string|max:255',
            'related_party_signature_name' => 'nullable|string|max:255',
            'related_party_signature_position' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Tanggal harus berupa tanggal yang valid.',
            'shift.required' => 'Sesi wajib diisi.',
            'shift.in' => 'Sesi tidak valid.',
            'requested_by.required' => 'Diajukan oleh wajib diisi.',
            'requested_by.string' => 'Diajukan oleh harus berupa teks.',
            'requested_by.max' => 'Diajukan oleh maksimal 255 karakter.',
            'document_link.string' => 'Tautan dokumen harus berupa teks.',
            'document_link.url' => 'Tautan dokumen harus berupa URL yang valid.',
            'document_link.max' => 'Tautan dokumen maksimal 255 karakter.',
            'meeting_room.string' => 'Ruang pertemuan harus berupa teks.',
            'meeting_room.max' => 'Ruang pertemuan maksimal 255 karakter.',
            'study_program.string' => 'Program studi harus berupa teks.',
            'study_program.max' => 'Program studi maksimal 255 karakter.',
            'participants.string' => 'Peserta harus berupa teks.',
            'participants.max' => 'Peserta maksimal 255 karakter.',
            'agenda.string' => 'Agenda harus berupa teks.',
            'city.string' => 'Kota harus berupa teks.',
            'city.max' => 'Kota maksimal 255 karakter.',
            'prodi_signature_name.string' => 'Nama penandatangan Prodi harus berupa teks.',
            'prodi_signature_name.max' => 'Nama penandatangan Prodi maksimal 255 karakter.',
            'prodi_signature_position.string' => 'Jabatan penandatangan Prodi harus berupa teks.',
            'prodi_signature_position.max' => 'Jabatan penandatangan Prodi maksimal 255 karakter.',
            'related_party_signature_name.string' => 'Nama penandatangan pihak terkait harus berupa teks.',
            'related_party_signature_name.max' => 'Nama penandatangan pihak terkait maksimal 255 karakter.',
            'related_party_signature_position.string' => 'Jabatan penandatangan pihak terkait harus berupa teks.',
            'related_party_signature_position.max' => 'Jabatan penandatangan pihak terkait maksimal 255 karakter.',
        ];
    }
}
