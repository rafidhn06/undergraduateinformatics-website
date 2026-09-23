<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReservationSubmissionRequest;
use App\Services\MsForms\MsFormsException;
use App\Services\Reservation\ReservationDocumentException;
use App\Services\Reservation\ReservationFormUnavailableException;
use App\Services\Reservation\ReservationIntake;
use App\Services\Reservation\ReservationMappingException;
use App\Services\Reservation\ReservationValidationException;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ReservationSubmissionController extends Controller
{
    public function __construct(private readonly ReservationIntake $intake)
    {
    }

    public function store(ReservationSubmissionRequest $request): JsonResponse
    {
        try {
            $schedule = $this->intake->submit($request->validated()['answers']);
        } catch (ReservationMappingException $e) {
            return response()->json(ApiResponse::error($e->getMessage(), $e->fieldErrors !== [] ? $e->fieldErrors : array_fill_keys($e->fields, ['This field is required.'])), 422);
        } catch (ReservationValidationException $e) {
            return response()->json(ApiResponse::error($e->getMessage(), $e->errors), 422);
        } catch (ReservationFormUnavailableException $e) {
            return response()->json(ApiResponse::error($e->getMessage()), 404);
        } catch (MsFormsException) {
            return response()->json(ApiResponse::error('Failed to submit the reservation. Please try again later.'), 422);
        } catch (ReservationDocumentException) {
            return response()->json(ApiResponse::error('Failed to save the reservation. Please try again later.'), 500);
        }

        return response()->json(ApiResponse::success($schedule), 201);
    }
}
