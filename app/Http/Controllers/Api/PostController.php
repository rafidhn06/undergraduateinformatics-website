<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Posts\PostsDataService;
use App\Services\Search\SearchDataService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = max((int) $request->query('page', 1), 1);
        $perPage = min(max((int) $request->query('per_page', 10), 1), 50);
        $q = $request->query('q');
        $q = is_string($q) ? $q : null;

        return response()->json(
            app(SearchDataService::class)->resolve($q, $page, $perPage)
        );
    }

    public function show(string $slugOrId): JsonResponse
    {
        try {
            $data = app(PostsDataService::class)->resolveDetail($slugOrId);
        } catch (ModelNotFoundException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found',
            ], 404);
        }

        return response()->json($data);
    }
}