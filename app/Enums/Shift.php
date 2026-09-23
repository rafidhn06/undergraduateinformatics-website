<?php

namespace App\Enums;

enum Shift: string
{
    case Morning = '09:00:00';
    case Midday = '13:00:00';
    case Afternoon = '15:00:00';

    public static function normalize(string $shift): ?self
    {
        return self::tryFrom(self::normalizeString($shift));
    }

    public static function normalizeString(string $shift): string
    {
        if (preg_match('/^(\d{2}:\d{2})/', $shift, $matches) === 1) {
            $shift = $matches[1];
        }

        if (strlen($shift) === 5) {
            $shift .= ':00';
        }

        return $shift;
    }
}
