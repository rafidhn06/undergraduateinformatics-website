<?php

namespace App\Services\Reservation;

final class ReservationValidationException extends ReservationException
{
    public function __construct(
        public readonly array $errors,
        string $message,
    ) {
        parent::__construct($message);
    }
}