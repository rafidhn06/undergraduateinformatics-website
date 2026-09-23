<?php

namespace App\Services\Posts;

use App\Http\Resources\PostResource;
use App\Http\Resources\PostSummaryResource;
use App\Models\Post;
use App\Services\Search\SearchDataService;
use App\Support\ApiResponse;
use App\Support\Pagination;

class PostsDataService
{
    public function __construct(private readonly SearchDataService $search)
    {
    }

    public function resolveList(PostListQuery $query): array
    {
        $posts = $this->search->resolve($query->q, $query->page, $query->perPage);

        return ApiResponse::success(
            PostSummaryResource::collection($posts)->resolve(),
            Pagination::meta($posts),
        );
    }

    public function resolveDetail(string $slugOrId): Post
    {
        return Post::with('tags')->whereSlugOrId($slugOrId)->firstOrFail();
    }

    public function resolveDetailPayload(string $slugOrId): array
    {
        return PostResource::make($this->resolveDetail($slugOrId))->resolve();
    }
}
