<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImportantLinkResource;
use App\Models\ImportantLink;
use App\Support\ApiResponse;
use App\Support\Pagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImportantLinkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = Pagination::page($request->query('page', 1));
        $perPage = Pagination::perPage($request->query('per_page', 10));
        $links = ImportantLink::latestPage($page, $perPage);

        return response()->json(ApiResponse::success(
            ImportantLinkResource::collection($links)->resolve(),
            Pagination::meta($links),
        ));
    }
}
