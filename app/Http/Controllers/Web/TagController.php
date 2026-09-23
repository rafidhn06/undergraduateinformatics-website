<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Tags\TagsDataService;
use App\Support\ApiResponse;
use App\Support\NotFoundResponse;
use App\Support\PageMeta;
use App\Support\PageSeed;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TagController extends Controller
{
    public function __construct(private readonly TagsDataService $tags)
    {
    }

    public function index(Request $request): View
    {
        $tagsData = $this->tags->resolveListPayload();

        $page = PageMeta::page('tagList');

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $page['title'],
            'url' => $request->url(),
            'description' => $page['description'],
        ];

        return view('app', PageMeta::viewData($request, 'tagList', $jsonLd, [
            PageSeed::entry('/api/tags', $tagsData),
        ]));
    }

    public function show(Request $request, string $slugOrId): View|Response
    {
        try {
            $tag = $this->tags->resolveDetailPayload($slugOrId);
        } catch (ModelNotFoundException) {
            return NotFoundResponse::view($request);
        }

        $tagData = ApiResponse::success($tag);

        $title = $tag['name'] . ' - ' . PageMeta::load()['defaultTitle'];
        $description = $tag['description'] ?? '';
        $metaDescription = $description !== '' ? $description : PageMeta::page('tagDetail')['description'];

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $title,
            'url' => $request->url(),
            'description' => $metaDescription,
        ];

        return view('app', PageMeta::viewData($request, 'tagDetail', $jsonLd, [
            PageSeed::entry('/api/tags/'.$slugOrId, $tagData),
        ], $title, $metaDescription));
    }
}
