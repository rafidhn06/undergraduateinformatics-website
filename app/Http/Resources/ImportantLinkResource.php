<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImportantLinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'link' => $this->link,
            'updated_at' => $this->updated_at?->toIso8601String(),
            'section' => $this->whenLoaded('important_section', fn () => [
                'id' => $this->important_section->id,
                'name' => $this->important_section->name,
            ]),
        ];
    }
}
