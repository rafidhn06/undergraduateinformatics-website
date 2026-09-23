<?php

namespace App\Services\Reservation;

use App\Models\ReservationLink;
use App\Models\ReservationSchedule;
use App\Services\MsForms\MsFormsClient;
use App\Services\MsForms\MsFormsException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

final class ReservationSubmissionService
{
    public function __construct(
        private readonly ReservationAnswerMapper $mapper,
        private readonly MsFormsClient $msFormsClient,
        private readonly ReservationSlotGuard $slots,
    ) {}

    public function submit(array $answers): ReservationSchedule
    {
        $attributes = $this->mapper->map($answers);

        $link = ReservationLink::configured()->first();

        if (! $link) {
            throw new ReservationFormUnavailableException('Reservation form is unavailable.');
        }

        ReservationScheduleValidator::assertValidDate($attributes['date']);
        ReservationScheduleValidator::assertValidShift($attributes['shift']);
        $this->slots->assertAvailable($attributes['date'], $attributes['shift']);

        try {
            $schedule = ReservationSchedule::create($attributes);
        } catch (\Throwable $e) {
            if ($this->isUniqueViolation($e)) {
                throw ReservationSlotGuard::alreadyFullException();
            }

            throw new ReservationDocumentException('Failed to save the reservation. Please try again later.', 0, $e);
        }

        try {
            $target = $this->msFormsClient->resolve($link->link);
            $this->msFormsClient->submitAnswers(
                $target,
                $this->msAnswers($answers),
                now()->toIso8601String()
            );
        } catch (MsFormsException $e) {
            Log::error('Reservation form submit failed: '.$e->getMessage());
            $schedule->delete();

            throw $e;
        }

        return $schedule;
    }

    private function isUniqueViolation(\Throwable $e): bool
    {
        $pdo = $e instanceof QueryException ? $e->getPrevious() : $e;

        return $pdo instanceof \PDOException && (string) $pdo->getCode() === '23000';
    }

    private function msAnswers(array $answers): array
    {
        return array_map(
            static fn (array $answer) => [
                'questionId' => $answer['questionId'],
                'answer1' => is_array($answer['answer'])
                    ? json_encode($answer['answer'])
                    : (string) $answer['answer'],
            ],
            $answers
        );
    }
}
