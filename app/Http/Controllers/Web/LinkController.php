<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImportantSectionResource;
use App\Models\ImportantSection;
use App\Support\ApiResponse;
use App\Support\PageMeta;
use App\Support\PageSeed;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function index(Request $request): View
    {
        $sections = ImportantSectionResource::collection(
            ImportantSection::orderedWithLinks()->get()
        )->resolve();

        $page = PageMeta::page('links');

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $page['title'],
            'url' => $request->url(),
            'description' => $page['description'],
        ];

        return view('app', PageMeta::viewData($request, 'links', $jsonLd, [
            PageSeed::entry('/api/link-sections', ApiResponse::success($sections)),
        ]));
    }
}
