<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImportantSectionResource;
use App\Models\ImportantSection;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ImportantSectionController extends Controller
{
    public function index(): JsonResponse
    {
        $sections = ImportantSection::orderedWithLinks()->get();

        return response()->json(
            ApiResponse::success(ImportantSectionResource::collection($sections)->resolve())
        );
    }
}
