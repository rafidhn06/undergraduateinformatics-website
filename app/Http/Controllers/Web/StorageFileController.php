<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageFileController extends Controller
{
    public function __invoke(string $path): BinaryFileResponse
    {
        $disk = Storage::disk('public');

        if ($path === '' || str_contains($path, '..')) {
            abort(404);
        }

        $absolute = $disk->path($path);

        if (!is_file($absolute) || !is_readable($absolute)) {
            abort(404);
        }

        return response()->file($absolute, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
