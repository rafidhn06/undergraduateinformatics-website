<?php

namespace App\Services\MsForms;

use App\Models\FeedbackLink;
use App\Models\MsFormDefinition;
use App\Models\ReservationLink;

final class FormDefinitionService
{
    public function resolve(string $kind): array
    {
        $row = MsFormDefinition::query()->where('kind', $kind)->first();

        if ($row === null || $row->payload === null) {
            throw new MsFormsException("No stored definition for {$kind}");
        }

        return $row->payload;
    }

    public function refresh(string $kind, ?string $link = null): array
    {
        $target = $link ?? $this->configuredLink($kind);

        if ($target === null || $target === '') {
            throw new MsFormsException("No configured link for {$kind}");
        }

        $data = $this->fetch($target);

        MsFormDefinition::query()->updateOrCreate(
            ['kind' => $kind],
            ['link' => $target, 'payload' => $data, 'fetched_at' => now()]
        );

        return $data;
    }

    private function configuredLink(string $kind): ?string
    {
        return $kind === 'reservation'
            ? ReservationLink::configured()->first()?->link
            : FeedbackLink::configured()->first()?->link;
    }

    private function fetch(string $link): array
    {
        $client = app(MsFormsClient::class);
        $target = $client->resolve($link);
        $raw = $client->fetchFormDefinition($target);
        $normalized = (new FormDefinitionNormalizer())->normalize($raw);

        return array_merge(['link' => $link], $normalized);
    }
}
