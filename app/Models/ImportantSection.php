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

    public function importantLinks(): HasMany
    {
        return $this->hasMany(ImportantLink::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function($query, $search){
            return $query -> where('important_sections.name', 'like', '%' . $search . '%');
        });
    }

    public function scopeOrderedWithLinks($query)
    {
        return $query->with(['importantLinks' => function ($query) {
            $query->orderByDesc('updated_at')->orderByDesc('id');
        }])->orderBy('order_number');
    }
}
