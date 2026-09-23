<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FeedbackSubmissionRequest;
use App\Models\FeedbackLink;
use App\Services\MsForms\MsFormsClient;
use App\Services\MsForms\MsFormsException;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FeedbackSubmissionController extends Controller
{
    public function __construct(private readonly MsFormsClient $forms)
    {
    }

    public function store(FeedbackSubmissionRequest $request): JsonResponse
    {
        $feedbackLink = FeedbackLink::query()->configured()->first();

        if (! $feedbackLink) {
            return response()->json(ApiResponse::error('Feedback form is unavailable.'), 404);
        }

        try {
            $target = $this->forms->resolve($feedbackLink->link);
            $msAnswers = array_map(
                fn (array $answer) => [
                    'questionId' => $answer['questionId'],
                    'answer1' => is_array($answer['answer']) ? json_encode($answer['answer']) : (string) $answer['answer'],
                ],
                $request->validated()['answers']
            );
            $now = now()->toIso8601String();
            $this->forms->submitAnswers($target, $msAnswers, $now);

            return response()->json(ApiResponse::success(['submitted_at' => $now]), 201);
        } catch (MsFormsException $e) {
            Log::error('Feedback form submit failed: '.$e->getMessage());

            return response()->json(ApiResponse::error('Failed to submit the form. Please try again later.'), 422);
        }
    }
}
