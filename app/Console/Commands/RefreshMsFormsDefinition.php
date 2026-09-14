<?php

namespace App\Console\Commands;

use App\Models\FeedbackLink;
use App\Models\ReservationLink;
use App\Services\MsForms\FormDefinitionService;
use App\Services\MsForms\MsFormsException;
use Illuminate\Console\Command;

final class RefreshMsFormsDefinition extends Command
{
    protected $signature = 'msforms:refresh-definition';

    protected $description = 'Write the MS Forms definitions to the database so user requests never fetch Microsoft';

    public function handle(): int
    {
        $failed = false;

        foreach (['feedback', 'reservation'] as $kind) {
            try {
                app(FormDefinitionService::class)->refresh($kind);
            } catch (MsFormsException) {
                $configured = $kind === 'reservation'
                    ? ReservationLink::configured()->first()
                    : FeedbackLink::configured()->first();

                if (! $configured) {
                    continue;
                }

                $this->warn("Unable to refresh the {$kind} MS Forms definition; the existing row is kept.");
                $failed = true;
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
