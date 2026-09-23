<?php

namespace App\Services\Reservation;

final class ReservationFormMapping
{
    public function mapping(): array
    {
        return config('reservation.form_mapping', []);
    }

    public function requiredFields(): array
    {
        return config('reservation.required_fields', []);
    }

    public function questionIdFor(string $field): ?string
    {
        $questionId = array_search($field, $this->mapping(), true);

        return $questionId !== false ? (string) $questionId : null;
    }

    public function fieldFor(string $questionId): ?string
    {
        $field = $this->mapping()[$questionId] ?? null;

        return is_string($field) ? $field : null;
    }
}
