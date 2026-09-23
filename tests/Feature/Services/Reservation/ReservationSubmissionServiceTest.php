<?php

namespace Tests\Feature\Services\Reservation;

use App\Models\ReservationLink;
use App\Models\ReservationSchedule;
use App\Services\MsForms\MsFormsClient;
use App\Services\MsForms\MsFormsException;
use App\Services\MsForms\MsFormsRequestException;
use App\Services\MsForms\ResolvedFormTarget;
use App\Services\Reservation\ReservationAnswerMapper;
use App\Services\Reservation\ReservationDocumentException;
use App\Services\Reservation\ReservationFormUnavailableException;
use App\Services\Reservation\ReservationMappingException;
use App\Services\Reservation\ReservationSlotGuard;
use App\Services\Reservation\ReservationSubmissionService;
use App\Services\Reservation\ReservationValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationSubmissionServiceTest extends TestCase
{
    use RefreshDatabase;

    private array $fullAnswers;

    protected function setUp(): void
    {
        parent::setUp();

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
        ]);

        $this->fullAnswers = [
            ['questionId' => 'r10000000000000000000000000000001', 'answer' => '2026-09-10'],
            ['questionId' => 'r10000000000000000000000000000002', 'answer' => '09:00'],
            ['questionId' => 'r10000000000000000000000000000003', 'answer' => 'Budi'],
            ['questionId' => 'r10000000000000000000000000000004', 'answer' => 'Ruang 101'],
            ['questionId' => 'r10000000000000000000000000000007', 'answer' => 'Rapat koordinasi'],
        ];
    }

    private function service(
        ?MsFormsClient $client = null,
    ): ReservationSubmissionService {
        $client ??= $this->mock(MsFormsClient::class, function ($mock) {
            $mock->shouldReceive('resolve')
                ->andReturn(new ResolvedFormTarget('https://forms.cloud.microsoft/formapi/api/x/users/y/light', 'FORM123'));
            $mock->shouldReceive('submitAnswers')->zeroOrMoreTimes();
        });

        return new ReservationSubmissionService(
            app(ReservationAnswerMapper::class),
            $client,
            app(ReservationSlotGuard::class),
        );
    }

    public function test_it_submits_to_ms_forms_and_persists_the_schedule(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $schedule = $this->service()->submit($this->fullAnswers);

        $this->assertNotNull($schedule);
        $this->assertSame('2026-09-10', $schedule->date->toDateString());
        $this->assertSame('09:00:00', $schedule->shift);
        $this->assertSame('Budi', $schedule->requested_by);
        $this->assertDatabaseHas('reservation_schedules', [
            'date' => '2026-09-10',
            'shift' => '09:00:00',
            'requested_by' => 'Budi',
        ]);
    }

    public function test_it_rejects_when_the_slot_is_already_full(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);
        ReservationSchedule::create([
            'date' => '2026-09-10',
            'shift' => '09:00:00',
            'requested_by' => 'Someone',
        ]);

        try {
            $this->service()->submit($this->fullAnswers);
            $this->fail('Expected ReservationValidationException was not thrown.');
        } catch (ReservationValidationException $e) {
            $this->assertSame('The schedule is already full.', $e->getMessage());
            $this->assertArrayHasKey('shift', $e->errors);
        }
    }

    public function test_it_rejects_a_date_that_is_not_on_an_allowed_day(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $answers = $this->fullAnswers;
        $answers[0] = ['questionId' => 'r10000000000000000000000000000001', 'answer' => '2026-09-09'];

        try {
            $this->service()->submit($answers);
            $this->fail('Expected ReservationValidationException was not thrown.');
        } catch (ReservationValidationException $e) {
            $this->assertArrayHasKey('date', $e->errors);
        }
    }

    public function test_it_rejects_a_shift_not_in_the_configured_list(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $answers = $this->fullAnswers;
        $answers[1] = ['questionId' => 'r10000000000000000000000000000002', 'answer' => '17:00'];

        try {
            $this->service()->submit($answers);
            $this->fail('Expected ReservationValidationException was not thrown.');
        } catch (ReservationValidationException $e) {
            $this->assertArrayHasKey('shift', $e->errors);
        }
    }

    public function test_it_rejects_a_shift_that_is_not_a_valid_time(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $answers = $this->fullAnswers;
        $answers[1] = ['questionId' => 'r10000000000000000000000000000002', 'answer' => 'sore'];

        try {
            $this->service()->submit($answers);
            $this->fail('Expected ReservationValidationException was not thrown.');
        } catch (ReservationValidationException $e) {
            $this->assertArrayHasKey('shift', $e->errors);
        }
    }

    public function test_it_does_not_persist_when_ms_forms_submission_fails(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $failingClient = $this->mock(MsFormsClient::class, function ($mock) {
            $mock->shouldReceive('resolve')
                ->andReturn(new ResolvedFormTarget('https://forms.cloud.microsoft/formapi/api/x/users/y/light', 'FORM123'));
            $mock->shouldReceive('submitAnswers')->once()->andThrow(new MsFormsRequestException('boom'));
        });

        try {
            $this->service($failingClient)->submit($this->fullAnswers);
            $this->fail('Expected MsFormsException was not thrown.');
        } catch (MsFormsException $e) {
            $this->assertSame('boom', $e->getMessage());
        }

        $this->assertDatabaseCount('reservation_schedules', 0);
    }

    public function test_it_throws_document_exception_when_db_insert_fails_before_ms_forms(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        ReservationSchedule::saving(function () {
            throw new \RuntimeException('database is down');
        });

        try {
            $this->service()->submit($this->fullAnswers);
            $this->fail('Expected ReservationDocumentException was not thrown.');
        } catch (ReservationDocumentException $e) {
            $this->assertSame('Failed to save the reservation. Please try again later.', $e->getMessage());
        }

        $this->assertDatabaseCount('reservation_schedules', 0);

        ReservationSchedule::flushEventListeners();
    }

    public function test_it_throws_404_when_no_reservation_link_is_configured(): void
    {
        ReservationLink::query()->delete();

        try {
            $this->service()->submit($this->fullAnswers);
            $this->fail('Expected ReservationFormUnavailableException was not thrown.');
        } catch (ReservationFormUnavailableException $e) {
            $this->assertSame('Reservation form is unavailable.', $e->getMessage());
        }
    }

    public function test_it_rejects_an_invalid_date_value(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $answers = $this->fullAnswers;
        $answers[0] = ['questionId' => 'r10000000000000000000000000000001', 'answer' => 'not-a-date'];

        try {
            $this->service()->submit($answers);
            $this->fail('Expected ReservationMappingException was not thrown.');
        } catch (ReservationMappingException $e) {
            $this->assertArrayHasKey('date', $e->fieldErrors);
        }
    }

    public function test_it_throws_slot_full_when_a_unique_violation_occurs_on_insert(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $pdoException = new \PDOException('SQLSTATE[23000]: Integrity constraint violation', 23000);
        $queryException = new QueryException('sqlite', 'insert into reservation_schedules', [], $pdoException);

        ReservationSchedule::saving(function () use ($queryException) {
            throw $queryException;
        });

        try {
            $this->service()->submit($this->fullAnswers);
            $this->fail('Expected ReservationValidationException was not thrown.');
        } catch (ReservationValidationException $e) {
            $this->assertSame('The schedule is already full.', $e->getMessage());
            $this->assertArrayHasKey('shift', $e->errors);
        }

        ReservationSchedule::flushEventListeners();
    }

    public function test_it_throws_document_exception_when_a_non_unique_db_failure_occurs_on_insert(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $queryException = new QueryException(
            'sqlite',
            'insert into reservation_schedules',
            [],
            new \PDOException('database is down')
        );

        ReservationSchedule::saving(function () use ($queryException) {
            throw $queryException;
        });

        try {
            $this->service()->submit($this->fullAnswers);
            $this->fail('Expected ReservationDocumentException was not thrown.');
        } catch (ReservationDocumentException $e) {
            $this->assertSame('Failed to save the reservation. Please try again later.', $e->getMessage());
        }

        ReservationSchedule::flushEventListeners();
    }
}
