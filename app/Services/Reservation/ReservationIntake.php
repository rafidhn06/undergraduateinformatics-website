<?php

namespace App\Services\Reservation;

use App\Models\ReservationSchedule;
use App\Services\MsForms\FormDefinitionService;

final class ReservationIntake
{
    public function __construct(
        private readonly FormDefinitionService $formDefinitions,
        private readonly ReservationMetadata $metadata,
        private readonly ReservationAvailabilityService $availability,
        private readonly ReservationSubmissionService $submissions,
    ) {
    }

    public function isAvailable(string $date, string $shift): bool
    {
        return $this->availability->isAvailable($date, $shift);
    }

    public function submit(array $answers): ReservationSchedule
    {
        return $this->submissions->submit($answers);
    }

    public function formPayload(): array
    {
        $payload = $this->formDefinitions->resolve('reservation');
        $payload['reservation'] = $this->metadata->build();

        return $payload;
    }

    public function reservationMetadata(): array
    {
        return $this->metadata->build();
    }
}
