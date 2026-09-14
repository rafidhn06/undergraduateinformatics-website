<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MsForms\MsFormsException;
use App\Services\Reservation\ReservationAvailabilityService;
use App\Services\Reservation\ReservationFormService;
use App\Services\Reservation\ReservationFormUnavailableException;
use App\Services\Reservation\ReservationMappingException;
use App\Services\Reservation\ReservationMetadata;
use App\Services\Reservation\ReservationSubmissionService;
use App\Services\Reservation\ReservationValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function show(): JsonResponse
    {
        try {
            $payload = app(ReservationFormService::class)->resolve();
        } catch (ReservationFormUnavailableException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);
        } catch (MsFormsException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reservation form is unavailable.',
            ], 404);
        }

        $payload['reservation'] = app(ReservationMetadata::class)->build();

        return response()->json([
            'status' => 'success',
            'data' => $payload,
        ]);
    }

    public function availability(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'shift' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $available = app(ReservationAvailabilityService::class)
                ->isAvailable($request->input('date'), $request->input('shift'));
        } catch (ReservationValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->errors,
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'data' => ['available' => $available],
        ]);
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
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $schedule = app(ReservationSubmissionService::class)->submit($validator->validated()['answers']);
        } catch (ReservationMappingException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->fieldErrors !== [] ? $e->fieldErrors : array_fill_keys($e->fields, ['This field is required.']),
            ], 422);
        } catch (ReservationValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->errors,
            ], 422);
        } catch (ReservationFormUnavailableException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);
        } catch (MsFormsException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to submit the reservation. Please try again later.',
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Reservation submitted successfully.',
            'data' => $schedule,
        ], 201);
    }
}