<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardDatasetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $items = $this->items;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'chart_type' => $this->chart_type,
            'labels' => $items->pluck('label')->values()->all(),
            'values' => $items->pluck('value')->values()->all(),
        ];
    }
}
