<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImportantLinkResource;
use App\Services\ImportantLinks\ImportantLinkQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImportantLinkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = max((int) $request->query('page', 1), 1);
        $limit = min(max((int) $request->query('limit', 10), 1), 50);
        $links = app(ImportantLinkQuery::class)->latest($page, $limit);

        return response()->json([
            'status' => 'success',
            'data' => ImportantLinkResource::collection($links)->resolve(),
            'meta' => [
                'current_page' => $links->currentPage(),
                'per_page' => $links->perPage(),
                'total' => $links->total(),
                'last_page' => $links->lastPage(),
            ],
        ]);
    }
}
