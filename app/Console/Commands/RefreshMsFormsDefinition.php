<?php

namespace App\Console\Commands;

use App\Services\MsForms\FormDefinitionService;
use App\Services\MsForms\MsFormsException;
use Illuminate\Console\Command;

final class RefreshMsFormsDefinition extends Command
{
    protected $signature = 'msforms:refresh-definition';

    protected $description = 'Write the MS Forms definitions to the database so user requests never fetch Microsoft';

    public function handle(FormDefinitionService $forms): int
    {
        $failed = false;

        foreach (['feedback', 'reservation'] as $kind) {
            try {
                $forms->refresh($kind);
            } catch (MsFormsException) {
                if (! $forms->isConfigured($kind)) {
                    continue;
                }

                $this->warn("Unable to refresh the {$kind} MS Forms definition; the existing row is kept.");
                $failed = true;
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
