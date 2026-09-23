<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostSearchRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $query = array_filter([
            'q' => is_string($request->query('q')) ? $request->query('q') : null,
            'per_page' => $request->query('per_page'),
            'page' => $request->query('page'),
        ]);

        return redirect()->to(route('posts.index', $query, false), 301);
    }
}
