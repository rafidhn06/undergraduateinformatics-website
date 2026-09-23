<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationSchedule extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    protected $fillable = [
        'date',
        'shift',
        'requested_by',
        'document_link',
        'meeting_room',
        'study_program',
        'participants',
        'agenda',
        'city',
        'prodi_signature_name',
        'prodi_signature_position',
        'related_party_signature_name',
        'related_party_signature_position',
    ];
}
