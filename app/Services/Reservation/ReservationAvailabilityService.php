<?php

namespace App\Services\Reservation;

use Illuminate\Support\Carbon;

final class ReservationAvailabilityService
{
    public function __construct(private readonly ReservationSlotGuard $slots)
    {
    }

    public function isAvailable(string $date, string $shift): bool
    {
        ReservationScheduleValidator::assertValidDate($date);
        ReservationScheduleValidator::assertValidShift($shift);

        $normalizedDate = Carbon::parse($date)->format('Y-m-d');
        $normalizedShift = ReservationScheduleValidator::normalizeShift($shift);

        return ! $this->slots->isTaken($normalizedDate, $normalizedShift);
    }
}
