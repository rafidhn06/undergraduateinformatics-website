<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatasetImport extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'token', 'status', 'payload', 'expires_at'];

    protected $casts = ['payload' => 'array', 'expires_at' => 'datetime'];

    public function getRouteKeyName(): string
    {
        return 'token';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
