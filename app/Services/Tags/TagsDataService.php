<?php

namespace App\Services\Tags;

use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Support\ApiResponse;
use App\Support\Pagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TagsDataService
{
    private function baseQuery(): Builder
    {
        return Tag::withCount('posts')
            ->withMax('posts', 'updated_at')
            ->orderByDesc('posts_max_updated_at')
            ->orderBy('name');
    }

    public function resolve(): Collection
    {
        return $this->baseQuery()->get();
    }

    public function resolveListPayload(mixed $page = null, mixed $perPage = null): array
    {
        $tags = $this->baseQuery()->paginate(
            Pagination::perPage($perPage ?? 50),
            ['*'],
            'page',
            Pagination::page($page)
        );

        return ApiResponse::success(
            TagResource::collection($tags)->resolve(),
            Pagination::meta($tags)
        );
    }

    public function resolveDetail(string $slugOrId): Tag
    {
        return Tag::with([
            'posts' => fn ($query) => $query->orderByDesc('posts.updated_at'),
            'posts.tags',
        ])->whereSlugOrId($slugOrId)->firstOrFail();
    }

    public function resolveDetailPayload(string $slugOrId): array
    {
        return TagResource::make($this->resolveDetail($slugOrId))->resolve();
    }
}
