<?php

namespace App\Services\Reservation;

use Illuminate\Support\Carbon;

final class ReservationAnswerMapper
{
    public function __construct(private readonly ReservationFormMapping $formMapping)
    {
    }

    public function map(array $answers): array
    {
        $mapping = $this->formMapping->mapping();
        $required = $this->formMapping->requiredFields();

        $byQuestionId = [];

        foreach ($answers as $answer) {
            $byQuestionId[$answer['questionId']] = $answer['answer'];
        }

        $missing = [];

        foreach ($required as $field) {
            $questionId = $this->formMapping->questionIdFor($field);
            $value = $questionId !== null ? ($byQuestionId[$questionId] ?? null) : null;

            if ($value === null || $value === '' || $value === []) {
                $missing[] = $field;
            }
        }

        if ($missing !== []) {
            throw new ReservationMappingException(
                $missing,
                [],
                'Required fields are missing: ' . implode(', ', $missing) . '.'
            );
        }

        $attributes = [];

        foreach ($mapping as $questionId => $field) {
            if (!array_key_exists($questionId, $byQuestionId)) {
                continue;
            }

            $value = $byQuestionId[$questionId];

            if ($value === null || $value === '' || $value === []) {
                continue;
            }

            $attributes[$field] = match ($field) {
                'date' => $this->normalizeDate($value),
                'shift' => $this->normalizeShift($value),
                default => $this->normalizeText($value),
            };
        }

        return $attributes;
    }

    private function normalizeDate(mixed $value): string
    {
        try {
            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable $e) {
            throw new ReservationMappingException(
                ['date'],
                ['date' => ['The reservation date is not a valid date.']],
                'The reservation date is not valid.'
            );
        }
    }

    private function normalizeShift(mixed $value): string
    {
        return ReservationScheduleValidator::normalizeShift((string) $value);
    }

    private function normalizeText(mixed $value): string
    {
        if (is_array($value)) {
            return implode(', ', $value);
        }

        return (string) $value;
    }
}
