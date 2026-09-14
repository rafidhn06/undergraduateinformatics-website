<?php

namespace Tests\Feature\Api;

use App\Models\ReservationLink;
use App\Models\ReservationSchedule;
use App\Services\MsForms\FormDefinitionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\Concerns\FakesMicrosoftForms;
use Tests\TestCase;

class ApiReservationControllerTest extends TestCase
{
    use FakesMicrosoftForms;
    use RefreshDatabase;

    private array $fullAnswers = [
        ['questionId' => 'r10000000000000000000000000000001', 'answer' => '2026-09-10'],
        ['questionId' => 'r10000000000000000000000000000002', 'answer' => '09:00'],
        ['questionId' => 'r10000000000000000000000000000003', 'answer' => 'Budi'],
        ['questionId' => 'r10000000000000000000000000000004', 'answer' => 'Ruang 101'],
        ['questionId' => 'r10000000000000000000000000000007', 'answer' => 'Rapat koordinasi'],
    ];

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
                'r10000000000000000000000000000004' => 'meeting_room',
                'r10000000000000000000000000000005' => 'study_program',
                'r10000000000000000000000000000006' => 'participants',
                'r10000000000000000000000000000007' => 'agenda',
                'r10000000000000000000000000000008' => 'city',
                'r10000000000000000000000000000009' => 'prodi_signature_name',
                'r10000000000000000000000000000010' => 'prodi_signature_position',
                'r10000000000000000000000000000011' => 'related_party_signature_name',
                'r10000000000000000000000000000012' => 'related_party_signature_position',
            ],
            'reservation.required_fields' => ['date', 'shift', 'requested_by'],
            'reservation.allowed_days' => [1, 2, 4, 5],
        ]);
    }

    public function test_get_reservation_returns_the_form_definition(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);
        app(FormDefinitionService::class)->refresh('reservation', 'https://forms.office.com/r/abc123');

        $this->getJson('/api/reservation')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.title.text', 'Reservation Form')
            ->assertJsonCount(12, 'data.questions');
    }

    public function test_get_reservation_returns_404_when_not_configured(): void
    {
        ReservationLink::query()->delete();

        $this->getJson('/api/reservation')
            ->assertStatus(404)
            ->assertJsonPath('status', 'error');
    }

    public function test_get_reservation_auto_refreshes_when_no_definition_is_stored(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $this->getJson('/api/reservation')
            ->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.title.text', 'Reservation Form');

        $this->assertDatabaseHas('ms_form_definitions', ['kind' => 'reservation']);
    }

    public function test_post_reservation_creates_a_schedule_and_submits_to_ms_forms(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $this->postJson('/api/reservation', ['answers' => $this->fullAnswers])
            ->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.date', '2026-09-10')
            ->assertJsonPath('data.shift', '09:00:00')
            ->assertJsonPath('data.requested_by', 'Budi');

        $this->assertDatabaseHas('reservation_schedules', [
            'date' => '2026-09-10',
            'shift' => '09:00:00',
            'requested_by' => 'Budi',
        ]);

        Http::assertSent(fn ($request) => str_contains($request->url(), '/responses'));
    }

    public function test_post_reservation_rejects_when_slot_is_full(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);
        ReservationSchedule::create([
            'date' => '2026-09-10',
            'shift' => '09:00:00',
            'requested_by' => 'Someone',
        ]);

        $this->postJson('/api/reservation', ['answers' => $this->fullAnswers])
            ->assertStatus(422)
            ->assertJsonPath('message', 'The schedule is already full.');

        Http::assertNotSent(fn ($request) => str_contains($request->url(), '/responses'));
    }

    public function test_post_reservation_validates_the_answers_shape(): void
    {
        $this->postJson('/api/reservation', ['answers' => [['questionId' => 'x']]])
            ->assertStatus(422)
            ->assertJsonPath('status', 'error');
    }

    public function test_post_reservation_rejects_when_a_required_field_is_missing(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $answers = array_values(array_filter(
            $this->fullAnswers,
            fn ($answer) => $answer['questionId'] !== 'r10000000000000000000000000000002'
        ));

        $this->postJson('/api/reservation', ['answers' => $answers])
            ->assertStatus(422)
            ->assertJsonPath('status', 'error');
    }

    public function test_post_reservation_returns_422_when_ms_forms_is_unreachable(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);
        $this->microsoftUnreachable = true;

        $this->postJson('/api/reservation', ['answers' => $this->fullAnswers])
            ->assertStatus(422)
            ->assertJsonPath('status', 'error');

        $this->assertDatabaseCount('reservation_schedules', 0);
    }

    public function test_post_reservation_returns_success_and_logs_critical_when_db_insert_fails(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        ReservationSchedule::saving(function () {
            throw new \RuntimeException('database is down');
        });

        Log::shouldReceive('critical')->once();

        $this->postJson('/api/reservation', ['answers' => $this->fullAnswers])
            ->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data', null);

        ReservationSchedule::flushEventListeners();
    }

    public function test_post_reservation_rejects_an_invalid_date(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $answers = $this->fullAnswers;
        $answers[0] = ['questionId' => 'r10000000000000000000000000000001', 'answer' => 'not-a-date'];

        $this->postJson('/api/reservation', ['answers' => $answers])
            ->assertStatus(422)
            ->assertJsonPath('status', 'error');
    }

    public function test_post_reservation_rejects_when_a_unique_violation_occurs_on_insert(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $pdoException = new \PDOException('SQLSTATE[23000]: Integrity constraint violation', 23000);
        $queryException = new \Illuminate\Database\QueryException('sqlite', 'insert into reservation_schedules', [], $pdoException);

        ReservationSchedule::saving(function () use ($queryException) {
            throw $queryException;
        });

        $this->postJson('/api/reservation', ['answers' => $this->fullAnswers])
            ->assertStatus(422)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'The schedule is already full.');

        ReservationSchedule::flushEventListeners();
    }

    public function test_get_reservation_includes_reservation_metadata(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);
        app(FormDefinitionService::class)->refresh('reservation', 'https://forms.office.com/r/abc123');

        $this->getJson('/api/reservation')
            ->assertOk()
            ->assertJsonPath('data.reservation.dateQuestionId', 'r10000000000000000000000000000001')
            ->assertJsonPath('data.reservation.shiftQuestionId', 'r10000000000000000000000000000002')
            ->assertJsonPath('data.reservation.allowedDays', [1, 2, 4, 5]);
    }

    public function test_get_reservation_availability_returns_available(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $this->getJson('/api/reservation/availability?date=2026-09-10&shift=09:00')
            ->assertOk()
            ->assertJsonPath('data.available', true);
    }

    public function test_get_reservation_availability_returns_unavailable(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);
        ReservationSchedule::create([
            'date' => '2026-09-10',
            'shift' => '09:00:00',
            'requested_by' => 'Someone',
        ]);

        $this->getJson('/api/reservation/availability?date=2026-09-10&shift=09:00')
            ->assertOk()
            ->assertJsonPath('data.available', false);
    }

    public function test_get_reservation_availability_accepts_a_range_shift_option(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $this->getJson('/api/reservation/availability?date=2026-09-10&shift=08:00 - 09:00 WIB')
            ->assertOk()
            ->assertJsonPath('data.available', true);
    }

    public function test_get_reservation_availability_range_shift_matches_stored_slot(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);
        ReservationSchedule::create([
            'date' => '2026-09-10',
            'shift' => '08:00:00',
            'requested_by' => 'Someone',
        ]);

        $this->getJson('/api/reservation/availability?date=2026-09-10&shift=08:00 - 09:00 WIB')
            ->assertOk()
            ->assertJsonPath('data.available', false);
    }

    public function test_get_reservation_availability_rejects_invalid_input(): void
    {
        $this->getJson('/api/reservation/availability?date=2026-09-09&shift=09:00')
            ->assertStatus(422)
            ->assertJsonPath('status', 'error');
    }
}
