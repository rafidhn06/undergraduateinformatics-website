<?php

namespace App\Services\Reservation;

use App\Enums\Shift;
use Illuminate\Support\Carbon;

final class ReservationTimetable
{
    public static function allowedDays(): array
    {
        return [1, 2, 4, 5];
    }

    public static function allowedShifts(): array
    {
        return array_map(static fn (Shift $shift) => $shift->value, Shift::cases());
    }

    public static function acceptedShiftInputs(): array
    {
        $inputs = [];

        foreach (Shift::cases() as $shift) {
            $inputs[] = substr($shift->value, 0, 5);
            $inputs[] = $shift->value;
        }

        return $inputs;
    }

    public static function isReservableDay(Carbon $date): bool
    {
        return in_array($date->dayOfWeekIso, self::allowedDays(), true);
    }
}
