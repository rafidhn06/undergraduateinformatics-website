<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Tags\TagsDataService;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function __construct(private readonly TagsDataService $tags)
    {
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->tags->resolveListPayload(
            $request->query('page'),
            $request->query('per_page')
        ));
    }

    public function show(string $slugOrId): JsonResponse
    {
        try {
            $tag = $this->tags->resolveDetailPayload($slugOrId);
        } catch (ModelNotFoundException) {
            return response()->json(ApiResponse::error('Tag not found'), 404);
        }

        return response()->json(ApiResponse::success($tag));
    }
}
