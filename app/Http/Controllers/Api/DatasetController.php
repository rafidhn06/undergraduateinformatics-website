<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardDatasetResource;
use App\Models\DashboardDataset;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class DatasetController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $datasets = DashboardDataset::query()->with(['items' => fn ($query) => $query->orderBy('sort_order')])->orderBy('id')->get();

        return response()->json(ApiResponse::success(DashboardDatasetResource::collection($datasets)->resolve()));
    }
}
