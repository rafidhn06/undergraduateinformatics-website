<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTrustedOrigin
{
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin') ?? $request->headers->get('Referer');

        if ($origin && ! $this->isTrustedOrigin($request, $origin)) {
            abort(403, 'Permintaan dari origin ini tidak diizinkan.');
        }

        return $next($request);
    }

    private function isTrustedOrigin(Request $request, string $origin): bool
    {
        $host = strtolower((string) parse_url($origin, PHP_URL_HOST));

        // Origin yang sama dengan host permintaan (berlaku otomatis di dev & prod).
        if ($host === strtolower($request->getHost())) {
            return true;
        }

        // Origin lokal untuk pengembangan.
        if (in_array($host, ['localhost', '127.0.0.1', '[::1]'], true) && app()->environment('local')) {
            return true;
        }

        // Origin tambahan dari konfigurasi CORS.
        foreach ((array) config('cors.allowed_origins', []) as $allowed) {
            if ($origin === $allowed) {
                return true;
            }
        }

        return false;
    }
}