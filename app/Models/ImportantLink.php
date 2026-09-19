<?php

namespace App\Models;

use App\Models\ImportantSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportantLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'important_section_id',
        'name',
        'link',
    ];

    public function important_section(): BelongsTo
    {
        return $this->belongsTo(ImportantSection::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function($query, $search){
            return $query -> where('important_links.name', 'like', '%' . request('search') . '%')
                -> orWhereHas('important_section', function($q) {
                    $q->where('name', 'like', '%' . request('search') . '%');
                });
        });
    }
}
