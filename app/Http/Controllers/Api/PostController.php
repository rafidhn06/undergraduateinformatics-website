<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Posts\PostListQuery;
use App\Services\Posts\PostsDataService;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(
        private readonly PostsDataService $posts,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->posts->resolveList(PostListQuery::fromArray($request->query()))
        );
    }

    public function show(string $slugOrId): JsonResponse
    {
        try {
            $post = $this->posts->resolveDetailPayload($slugOrId);
        } catch (ModelNotFoundException) {
            return response()->json(ApiResponse::error('Post not found'), 404);
        }

        return response()->json(ApiResponse::success($post));
    }
}