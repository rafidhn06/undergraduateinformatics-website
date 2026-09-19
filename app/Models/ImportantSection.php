<?php

namespace App\Models;

use App\Models\ImportantLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportantSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'order_number',
    ];

    public function important_links(): HasMany
    {
        return $this->hasMany(ImportantLink::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function($query, $search){
            return $query -> where('name', 'like', '%' . request('search') . '%');
        });
    }
}
