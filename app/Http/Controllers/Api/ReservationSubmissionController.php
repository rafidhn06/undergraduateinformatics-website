<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReservationSubmissionRequest;
use App\Services\MsForms\MsFormsException;
use App\Services\Reservation\ReservationFormUnavailableException;
use App\Services\Reservation\ReservationMappingException;
use App\Services\Reservation\ReservationSubmissionService;
use App\Services\Reservation\ReservationValidationException;
use Illuminate\Http\JsonResponse;

class ReservationSubmissionController extends Controller
{
    public function store(ReservationSubmissionRequest $request): JsonResponse
    {
        try {
            $schedule = app(ReservationSubmissionService::class)->submit($request->validated()['answers']);
        } catch (ReservationMappingException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'errors' => $e->fieldErrors !== [] ? $e->fieldErrors : array_fill_keys($e->fields, ['This field is required.'])], 422);
        } catch (ReservationValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'errors' => $e->errors], 422);
        } catch (ReservationFormUnavailableException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 404);
        } catch (MsFormsException) {
            return response()->json(['status' => 'error', 'message' => 'Failed to submit the reservation. Please try again later.'], 422);
        }

        return response()->json(['status' => 'success', 'message' => 'Reservation submitted successfully.', 'data' => $schedule], 201);
    }
}
