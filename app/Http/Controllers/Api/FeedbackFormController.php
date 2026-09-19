<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeedbackLink;
use App\Services\MsForms\FormDefinitionService;
use App\Services\MsForms\MsFormsException;
use Illuminate\Http\JsonResponse;

class FeedbackFormController extends Controller
{
    public function show(): JsonResponse
    {
        $feedbackLink = FeedbackLink::query()->configured()->first();

        if (! $feedbackLink) {
            return response()->json(['status' => 'error', 'message' => 'Feedback form is unavailable.'], 404);
        }

        try {
            $payload = app(FormDefinitionService::class)->resolve('feedback');

            return response()->json(['status' => 'success', 'data' => $payload]);
        } catch (MsFormsException) {
            return response()->json(['status' => 'error', 'message' => 'Feedback form is unavailable.'], 404);
        }
    }
}
