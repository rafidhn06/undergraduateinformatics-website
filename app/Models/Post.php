<?php

namespace App\Models;

use App\Models\PostTag;
use App\Support\Slug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

        static::deleting(function (Post $post) {
            $post->clearImage();
        });
    }

    public function scopeWhereSlugOrId($query, string $value)
    {
        return $query->where('slug', $value)->orWhere('id', $value);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function($query, $search){
            return $query->where(function($query) use ($search) {
                $query->where('posts.title', 'like', '%' . $search . '%')
                    ->orWhere('posts.body', 'like', '%' . $search . '%');
            });
        });
    }

    public function postTags(): HasMany
    {
        return $this->hasMany(PostTag::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tags', 'post_id', 'tag_id')->using(PostTag::class);
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

    public function syncTags(array $tagIds): void
    {
        $ids = array_unique($tagIds);

        $tagDefault = Tag::where('name', 'S1 Informatika')->first();

        array_unshift($ids, $tagDefault->id);

        $this->tags()->sync(array_unique($ids));
    }

    public function replaceImage(?UploadedFile $file, bool $remove = false): void
    {
        if ($file) {
            $this->clearImage();
            $this->image = Storage::disk('public')->putFile('posts', $file);

            return;
        }

        if ($remove) {
            $this->clearImage();
        }
    }

    public function clearImage(): void
    {
        if (!$this->hasImage()) {
            return;
        }

        $shared = static::where('image', $this->image);

        if ($this->exists) {
            $shared->where('id', '!=', $this->id);
        }

        if ($shared->doesntExist()) {
            Storage::disk('public')->delete($this->image);
        }

        $this->image = null;
    }
}
