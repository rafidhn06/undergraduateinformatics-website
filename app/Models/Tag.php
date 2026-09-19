<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\Slug;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected static function booted(): void
    {
        static::saving(function (Tag $tag) {
            if ($tag->slug) {
                return;
            }

            $tag->slug = Slug::makeUnique(Str::slug($tag->name) ?: 'tag', function (string $slug) use ($tag) {
                $query = Tag::where('slug', $slug);

                if ($tag->exists) {
                    $query->where('id', '!=', $tag->id);
                }

                return $query->exists();
            });
        });
    }

    public function scopeWhereSlugOrId($query, string $value)
    {
        return $query->where('slug', $value)->orWhere('id', $value);
    }

    public function post_tags(): HasMany
    {
        return $this->hasMany(PostTag::class);
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tags', 'tag_id', 'post_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function($query, $search){
            return $query -> where('name', 'like', '%' . request('search') . '%')
                        -> orWhere('description', 'like', '%' . request('search') . '%');
        });
    }
}
