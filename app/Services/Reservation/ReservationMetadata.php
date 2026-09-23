<?php

namespace App\Services\Reservation;

final class ReservationMetadata
{
    public function __construct(private readonly ReservationFormMapping $formMapping)
    {
    }

    public function build(): array
    {
        return [
            'dateQuestionId' => (string) $this->formMapping->questionIdFor('date'),
            'shiftQuestionId' => (string) $this->formMapping->questionIdFor('shift'),
            'allowedDays' => ReservationTimetable::allowedDays(),
        ];
    }
}