<?php

namespace App\Services\Search;

use App\Http\Resources\PostSummaryResource;
use App\Models\Post;

class SearchDataService
{
    public function resolve(?string $q, int $page, int $perPage): array
    {
        $query = Post::query()
            ->with('tags')
            ->select(['id', 'slug', 'title', 'subtitle', 'updated_at'])
            ->orderByDesc('updated_at');

        if ($q !== null && $q !== '') {
            $like = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $q).'%';
            $query->where(function ($query) use ($like) {
                $query->whereRaw("title LIKE ? ESCAPE '!'", [$like])
                    ->orWhereRaw("subtitle LIKE ? ESCAPE '!'", [$like])
                    ->orWhereRaw("body LIKE ? ESCAPE '!'", [$like]);
            });
        }

        $posts = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'status' => 'success',
            'data' => PostSummaryResource::collection($posts)->resolve(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'last_page' => $posts->lastPage(),
            ],
        ];
    }
}
