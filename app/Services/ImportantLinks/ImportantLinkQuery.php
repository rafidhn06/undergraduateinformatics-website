<?php

namespace App\Services\ImportantLinks;

use App\Models\ImportantLink;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ImportantLinkQuery
{
    public function latest(int $page, int $perPage): LengthAwarePaginator
    {
        return ImportantLink::query()
            ->with('important_section')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
