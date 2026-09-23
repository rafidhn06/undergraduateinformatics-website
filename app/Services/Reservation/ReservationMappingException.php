<?php

namespace App\Services\Reservation;

final class ReservationMappingException extends ReservationException
{
    public function __construct(
        public readonly array $fields,
        public readonly array $fieldErrors = [],
        string $message = '',
    ) {
        parent::__construct($message !== '' ? $message : 'Reservation mapping failed.');
    }
}