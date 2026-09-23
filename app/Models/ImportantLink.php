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

    public function importantSection(): BelongsTo
    {
        return $this->belongsTo(ImportantSection::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function($query, $search){
            return $query->where(function($query) use ($search) {
                $query->where('important_links.name', 'like', '%' . $search . '%')
                    ->orWhereHas('importantSection', function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        });
    }

    public function scopeLatestPage($query, int $page, int $perPage)
    {
        return $query->with('importantSection')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
