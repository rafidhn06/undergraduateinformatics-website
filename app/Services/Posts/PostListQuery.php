<?php

namespace App\Services\Posts;

use App\Support\Pagination;

final class PostListQuery
{
    public function __construct(
        public readonly ?string $q,
        public readonly int $page,
        public readonly int $perPage,
    ) {
    }

    public static function fromArray(array $query): self
    {
        $q = $query['q'] ?? null;

        return new self(
            is_string($q) ? $q : null,
            Pagination::page($query['page'] ?? null),
            Pagination::perPage($query['per_page'] ?? null),
        );
    }
}
