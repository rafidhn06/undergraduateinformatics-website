<?php

namespace Tests\Feature\Services\Reservation;

use App\Models\ReservationSchedule;
use App\Services\Reservation\ReservationAvailabilityService;
use App\Services\Reservation\ReservationValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationAvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReservationAvailabilityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ReservationAvailabilityService::class);
    }

    public function test_it_returns_true_when_the_slot_is_free(): void
    {
        $this->assertTrue($this->service->isAvailable('2026-09-10', '09:00:00'));
    }

    public function test_it_returns_false_when_the_slot_is_taken(): void
    {
        ReservationSchedule::create([
            'date' => '2026-09-10',
            'shift' => '09:00:00',
            'requested_by' => 'Someone',
        ]);

        $this->assertFalse($this->service->isAvailable('2026-09-10', '09:00:00'));
        $this->assertTrue($this->service->isAvailable('2026-09-10', '13:00:00'));
    }

    public function test_it_accepts_hh_mm_shift(): void
    {
        $this->assertTrue($this->service->isAvailable('2026-09-10', '09:00'));
    }

    public function test_it_rejects_a_shift_outside_the_offered_list(): void
    {
        $this->expectException(ReservationValidationException::class);

        $this->service->isAvailable('2026-09-10', '08:00 - 09:00 WIB');
    }

    public function test_it_normalizes_a_datetime_string_date_before_querying(): void
    {
        $this->assertTrue($this->service->isAvailable('2026-09-10 00:00:00', '09:00:00'));

        ReservationSchedule::create([
            'date' => '2026-09-10',
            'shift' => '09:00:00',
            'requested_by' => 'Someone',
        ]);

        $this->assertFalse($this->service->isAvailable('2026-09-10 00:00:00', '09:00:00'));
    }

    public function test_it_rejects_an_invalid_date(): void
    {
        $this->expectException(ReservationValidationException::class);
        $this->service->isAvailable('not-a-date', '09:00:00');
    }

    public function test_it_rejects_a_disallowed_day(): void
    {
        $this->expectException(ReservationValidationException::class);
        $this->service->isAvailable('2026-09-09', '09:00:00');
    }

    public function test_it_rejects_an_invalid_shift(): void
    {
        $this->expectException(ReservationValidationException::class);
        $this->service->isAvailable('2026-09-10', 'sore');
    }
}
