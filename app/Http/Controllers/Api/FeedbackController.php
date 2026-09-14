<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeedbackLink;
use App\Services\MsForms\FormDefinitionService;
use App\Services\MsForms\MsFormsClient;
use App\Services\MsForms\MsFormsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function show(): JsonResponse
    {
        $feedbackLink = FeedbackLink::configured()->first();

        if (!$feedbackLink) {
            return response()->json([
                'status' => 'error',
                'message' => 'Feedback form is unavailable.',
            ], 404);
        }

        try {
            $payload = app(FormDefinitionService::class)->resolve('feedback');

            return response()->json([
                'status' => 'success',
                'data' => $payload,
            ]);
        } catch (MsFormsException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Feedback form is unavailable.',
            ], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.questionId' => ['required', 'string'],
            'answers.*.answer' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $feedbackLink = FeedbackLink::configured()->first();

        if (!$feedbackLink) {
            return response()->json([
                'status' => 'error',
                'message' => 'Feedback form is unavailable.',
            ], 404);
        }

        try {
            $client = app(MsFormsClient::class);
            $target = $client->resolve($feedbackLink->link);

            $msAnswers = array_map(
                fn (array $answer) => [
                    'questionId' => $answer['questionId'],
                    'answer1' => is_array($answer['answer'])
                        ? json_encode($answer['answer'])
                        : (string) $answer['answer'],
                ],
                $validator->validated()['answers']
            );

            $now = now()->toIso8601String();
            $client->submitAnswers($target, $msAnswers, $now);

            return response()->json([
                'status' => 'success',
                'message' => 'Feedback submitted successfully.',
            ]);
        } catch (MsFormsException $e) {
            Log::error('Feedback form submit failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to submit the form. Please try again later.',
            ], 422);
        }
    }
}
