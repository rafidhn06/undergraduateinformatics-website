<?php

namespace App\Models;

use App\Models\PostTag;
use App\Support\Slug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'body',
        'image',
        'slug',
    ];

    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            if ($post->slug) {
                return;
            }

            $post->slug = Slug::makeUnique(Str::slug($post->title) ?: 'post', function (string $slug) use ($post) {
                $query = Post::where('slug', $slug);

                if ($post->exists) {
                    $query->where('id', '!=', $post->id);
                }

                return $query->exists();
            });
        });
    }

    public function scopeWhereSlugOrId($query, string $value)
    {
        return $query->where('slug', $value)->orWhere('id', $value);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function($query, $search){
            return $query -> where('title', 'like', '%' . request('search') . '%')
                        -> orWhere('body', 'like', '%' . request('search') . '%');
        });
    }

    public function post_tags(): HasMany
    {
        return $this->hasMany(PostTag::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tags', 'post_id', 'tag_id');
    }

    /**
     * Check if post has been edited or not
     */
    public function hasBeenUpdated()
    {
        return $this->created_at != $this->updated_at;
    }

    /**
     * Check if post has attached image
     */
    public function hasImage()
    {
        return $this->image !== null;
    }
}
