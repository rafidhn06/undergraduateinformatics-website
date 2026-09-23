<?php

namespace App\Http\Resources;

use App\Support\PostBodySanitizer;
use App\Support\PostImageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'body' => app(PostBodySanitizer::class)->sanitize($this->body),
            'image' => PostImageUrl::for($this->image),
            'tags' => TagSummaryResource::collection($this->whenLoaded('tags')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
