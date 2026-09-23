<?php

namespace Tests\Unit\Services\Reservation;

use App\Services\Reservation\ReservationScheduleValidator;
use App\Services\Reservation\ReservationValidationException;
use Tests\TestCase;

class ReservationScheduleValidatorTest extends TestCase
{
    public function test_normalize_shift_extracts_start_of_range(): void
    {
        $this->assertSame('08:00:00', ReservationScheduleValidator::normalizeShift('08:00 - 09:00 WIB'));
    }

    public function test_assert_valid_shift_rejects_time_outside_the_offered_list(): void
    {
        try {
            ReservationScheduleValidator::assertValidShift('08:00 - 09:00 WIB');
            $this->fail('Expected ReservationValidationException was not thrown.');
        } catch (ReservationValidationException $e) {
            $this->assertArrayHasKey('shift', $e->errors);
        }
    }

}
