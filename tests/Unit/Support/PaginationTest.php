<?php

namespace Tests\Unit\Support;

use App\Support\Pagination;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\TestCase;

final class PaginationTest extends TestCase
{
    public function test_page_clamps_to_minimum_one(): void
    {
        $this->assertSame(1, Pagination::page(null));
        $this->assertSame(1, Pagination::page(0));
        $this->assertSame(1, Pagination::page(-3));
        $this->assertSame(2, Pagination::page(2));
    }

    public function test_per_page_defaults_and_clamps_to_range(): void
    {
        $this->assertSame(10, Pagination::perPage(null));
        $this->assertSame(1, Pagination::perPage(0));
        $this->assertSame(50, Pagination::perPage(999));
        $this->assertSame(5, Pagination::perPage(5));
    }

    public function test_meta_maps_paginator_shape(): void
    {
        $paginator = new LengthAwarePaginator([1, 2], 42, 10, 2);

        $this->assertSame(
            ['current_page' => 2, 'per_page' => 10, 'total' => 42, 'last_page' => 5],
            Pagination::meta($paginator)
        );
    }
}
