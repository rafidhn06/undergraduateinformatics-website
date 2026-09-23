<?php

namespace App\Support;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class NotFoundResponse
{
    public static function view(Request $request): Response
    {
        return response()->view(
            'app',
            PageMeta::viewData($request, 'notFound', [], [], null, null),
            404
        );
    }
}
