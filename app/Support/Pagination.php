<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;

final class Pagination
{
    public const DEFAULT_PER_PAGE = 10;
    public const MAX_PER_PAGE = 50;

    public static function page(mixed $value): int
    {
        return max((int) ($value ?? 1), 1);
    }

    public static function perPage(mixed $value): int
    {
        return min(max((int) ($value ?? self::DEFAULT_PER_PAGE), 1), self::MAX_PER_PAGE);
    }

    public static function meta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
