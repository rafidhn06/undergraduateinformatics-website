<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Support\NotFoundResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FallbackController extends Controller
{
    public function __invoke(Request $request): Response
    {
        if ($request->is('admin/*') || $request->is('api/*')) {
            abort(404);
        }

        return NotFoundResponse::view($request);
    }
}
