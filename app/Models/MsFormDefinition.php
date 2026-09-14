<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsFormDefinition extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'payload' => 'array',
        'fetched_at' => 'datetime',
    ];
}
