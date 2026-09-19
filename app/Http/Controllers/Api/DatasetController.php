<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardDatasetResource;
use App\Models\DashboardDataset;
use Illuminate\Http\JsonResponse;

class DatasetController extends Controller
{
    public function index(): JsonResponse
    {
        $datasets = DashboardDataset::query()->with('items')->orderBy('id')->get();

        return response()->json([
            'status' => 'success',
            'data' => DashboardDatasetResource::collection($datasets)->resolve(),
        ]);
    }
}
