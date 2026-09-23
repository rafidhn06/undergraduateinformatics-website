<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MsForms\FormDefinitionService;
use App\Services\MsForms\MsFormsException;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class FeedbackFormController extends Controller
{
    public function __construct(private readonly FormDefinitionService $forms)
    {
    }

    public function show(): JsonResponse
    {
        try {
            $payload = $this->forms->resolve('feedback');

            return response()->json(ApiResponse::success($payload));
        } catch (MsFormsException) {
            return response()->json(ApiResponse::error('Feedback form is unavailable.'), 404);
        }
    }
}
