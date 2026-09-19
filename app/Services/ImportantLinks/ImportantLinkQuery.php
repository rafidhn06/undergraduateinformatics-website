<?php

namespace App\Services\ImportantLinks;

use App\Models\ImportantLink;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ImportantLinkQuery
{
    public function latest(int $page, int $limit): LengthAwarePaginator
    {
        return ImportantLink::query()
            ->with('important_section')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate($limit, ['*'], 'page', $page);
    }
}
