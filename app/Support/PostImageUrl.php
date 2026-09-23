<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class PostImageUrl
{
    public static function for(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        return asset('storage/' . $path);
    }
}
