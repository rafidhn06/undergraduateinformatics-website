<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MsForms\MsFormsException;
use App\Services\Reservation\ReservationAvailabilityService;
use App\Services\Reservation\ReservationFormService;
use App\Services\Reservation\ReservationFormUnavailableException;
use App\Services\Reservation\ReservationMetadata;
use App\Services\Reservation\ReservationValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationFormController extends Controller
{
    public function show(): JsonResponse
    {
        try {
            $payload = app(ReservationFormService::class)->resolve();
        } catch (ReservationFormUnavailableException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 404);
        } catch (MsFormsException) {
            return response()->json(['status' => 'error', 'message' => 'Reservation form is unavailable.'], 404);
        }

        $payload['reservation'] = app(ReservationMetadata::class)->build();

        return response()->json(['status' => 'success', 'data' => $payload]);
    }

    public function availability(Request $request): JsonResponse
    {
        $validated = $request->validate(['date' => ['required', 'date'], 'shift' => ['required']]);

        try {
            $available = app(ReservationAvailabilityService::class)->isAvailable($validated['date'], $validated['shift']);
        } catch (ReservationValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'errors' => $e->errors], 422);
        }

        return response()->json(['status' => 'success', 'data' => ['available' => $available]]);
    }
}
