<?php

namespace Tests\Feature\Services\Reservation;

use App\Models\ReservationLink;
use App\Models\ReservationSchedule;
use App\Services\Reservation\ReservationIntake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\FakesMicrosoftForms;
use Tests\TestCase;

class ReservationIntakeTest extends TestCase
{
    use FakesMicrosoftForms;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->runtimeFixture = 'reservation-form.raw.json';

        Http::preventStrayRequests();
        Http::fake($this->microsoftEndpoints());
        Cache::flush();

        config([
            'reservation.form_mapping' => [
                'r10000000000000000000000000000001' => 'date',
                'r10000000000000000000000000000002' => 'shift',
                'r10000000000000000000000000000003' => 'requested_by',
                'r10000000000000000000000000000007' => 'agenda',
            ],
            'reservation.required_fields' => ['date', 'shift', 'requested_by'],
        ]);
    }

    public function test_it_reports_availability_through_one_interface(): void
    {
        $intake = app(ReservationIntake::class);

        $this->assertTrue($intake->isAvailable('2026-09-10', '09:00'));

        ReservationSchedule::create([
            'date' => '2026-09-10',
            'shift' => '09:00:00',
            'requested_by' => 'Someone',
        ]);

        $this->assertFalse($intake->isAvailable('2026-09-10', '09:00'));
    }

    public function test_it_submits_through_one_interface(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $schedule = app(ReservationIntake::class)->submit([
            ['questionId' => 'r10000000000000000000000000000001', 'answer' => '2026-09-10'],
            ['questionId' => 'r10000000000000000000000000000002', 'answer' => '09:00'],
            ['questionId' => 'r10000000000000000000000000000003', 'answer' => 'Budi'],
            ['questionId' => 'r10000000000000000000000000000007', 'answer' => 'Rapat'],
        ]);

        $this->assertSame('2026-09-10', $schedule->date->format('Y-m-d'));
        $this->assertSame('09:00:00', $schedule->shift);
        $this->assertDatabaseHas('reservation_schedules', ['requested_by' => 'Budi']);
    }

    public function test_it_resolves_form_payload_with_metadata(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $payload = app(ReservationIntake::class)->formPayload();

        $this->assertArrayHasKey('reservation', $payload);
        $this->assertSame('r10000000000000000000000000000001', $payload['reservation']['dateQuestionId']);
        $this->assertSame([1, 2, 4, 5], $payload['reservation']['allowedDays']);
    }
}
