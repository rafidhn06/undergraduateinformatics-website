<?php

namespace App\Support;

final class PageSeed
{
    public static function entry(string $endpoint, mixed $payload, array $params = []): array
    {
        return [
            'endpoint' => $endpoint,
            'params' => $params,
            'payload' => $payload,
        ];
    }
}
