<?php

namespace Tests\Unit\Enums;

use App\Enums\Shift;
use App\Services\Reservation\ReservationTimetable;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ShiftTest extends TestCase
{
    public function test_normalize_accepts_offered_shifts_in_short_and_full_form(): void
    {
        $this->assertSame(Shift::Morning, Shift::normalize('09:00'));
        $this->assertSame(Shift::Midday, Shift::normalize('13:00:00'));
        $this->assertSame(Shift::Afternoon, Shift::normalize('15:00'));
    }

    public function test_normalize_rejects_unknown_shift(): void
    {
        $this->assertNull(Shift::normalize('08:00 - 09:00 WIB'));
        $this->assertNull(Shift::normalize('sore'));
    }

    public function test_timetable_lists_offered_days_and_shifts(): void
    {
        $this->assertSame([1, 2, 4, 5], ReservationTimetable::allowedDays());
        $this->assertSame(['09:00:00', '13:00:00', '15:00:00'], ReservationTimetable::allowedShifts());
    }

    public function test_timetable_rejects_weekend(): void
    {
        $this->assertTrue(ReservationTimetable::isReservableDay(Carbon::parse('2026-09-10')));
        $this->assertFalse(ReservationTimetable::isReservableDay(Carbon::parse('2026-09-12')));
    }
}
