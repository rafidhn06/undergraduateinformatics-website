<?php

namespace App\Services\Reservation;

use App\Enums\Shift;
use Illuminate\Support\Carbon;

final class ReservationScheduleValidator
{
    public static function normalizeShift(string $shift): string
    {
        return Shift::normalizeString($shift);
    }

    public static function assertValidDate(string $date): void
    {
        try {
            $parsed = Carbon::parse($date);
        } catch (\Throwable $e) {
            throw new ReservationValidationException(
                ['date' => ['The reservation date is not a valid date.']],
                'The reservation date is not valid.'
            );
        }

        if (! ReservationTimetable::isReservableDay($parsed)) {
            throw new ReservationValidationException(
                ['date' => ['The reservation date must be a Monday, Tuesday, Thursday, or Friday.']],
                'The reservation date is not available.'
            );
        }
    }

    public static function assertValidShift(string $shift): void
    {
        if (Shift::normalize($shift) === null) {
            throw new ReservationValidationException(
                ['shift' => ['The selected session is not available.']],
                'The selected session is not available.'
            );
        }
    }
}
