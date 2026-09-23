<?php

namespace App\Services\Reservation;

use App\Models\ReservationSchedule;

final class ReservationSlotGuard
{
    public function isTaken(string $date, string $shift): bool
    {
        return ReservationSchedule::query()
            ->where('date', $date)
            ->where('shift', $shift)
            ->exists();
    }

    public function assertAvailable(string $date, string $shift): void
    {
        if ($this->isTaken($date, $shift)) {
            throw self::alreadyFullException();
        }
    }

    public static function alreadyFullException(): ReservationValidationException
    {
        return new ReservationValidationException(
            ['shift' => ['The schedule on this date and session is already full. Please select another date or session.']],
            'The schedule is already full.'
        );
    }
}
