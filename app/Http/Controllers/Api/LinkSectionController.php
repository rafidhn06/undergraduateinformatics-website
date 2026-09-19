<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Links\LinksDataService;
use Illuminate\Http\JsonResponse;

class LinkSectionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(app(LinksDataService::class)->getSectionsWithLinks());
    }
}
