<?php

namespace App\Services\Reservation;

use App\Models\ReservationLink;
use App\Services\MsForms\FormDefinitionService;

final class ReservationFormService
{
    public function resolve(): array
    {
        $link = ReservationLink::configured()->first();

        if (! $link) {
            throw new ReservationFormUnavailableException('Reservation form is unavailable.');
        }

        return app(FormDefinitionService::class)->resolve('reservation');
    }
}
