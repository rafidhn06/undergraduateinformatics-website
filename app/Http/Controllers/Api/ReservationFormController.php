<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReservationAvailabilityRequest;
use App\Services\MsForms\MsFormsException;
use App\Services\Reservation\ReservationIntake;
use App\Services\Reservation\ReservationValidationException;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ReservationFormController extends Controller
{
    public function __construct(
        private readonly ReservationIntake $intake,
    ) {
    }

    public function show(): JsonResponse
    {
        try {
            $payload = $this->intake->formPayload();
        } catch (MsFormsException) {
            return response()->json(ApiResponse::error('Reservation form is unavailable.'), 404);
        }

        return response()->json(ApiResponse::success($payload));
    }

    public function availability(ReservationAvailabilityRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $available = $this->intake->isAvailable($validated['date'], $validated['shift']);
        } catch (ReservationValidationException $e) {
            return response()->json(ApiResponse::error($e->getMessage(), $e->errors), 422);
        }

        return response()->json(ApiResponse::success(['available' => $available]));
    }
}
