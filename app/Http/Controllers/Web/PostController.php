<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Posts\PostsDataService;
use App\Services\Search\SearchDataService;
use App\Support\PageMeta;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->query('q');
        $q = is_string($q) ? $q : null;
        $page = max((int) $request->query('page', 1), 1);
        $perPage = min(max((int) $request->query('per_page', 10), 1), 50);
        $payload = app(SearchDataService::class)->resolve($q, $page, $perPage);

        return view('app', PageMeta::viewData($request, 'postSearch', [], $payload));
    }

    public function show(Request $request, string $slugOrId): View|Response
    {
        try {
            $postData = app(PostsDataService::class)->resolveDetail($slugOrId);
        } catch (ModelNotFoundException) {
            return response()->view(
                'app',
                PageMeta::viewData($request, 'notFound', [], ['notFound' => true], null, null),
                404
            );
        }

        $post = $postData['data'];

        $title = $post['title'] . ' - ' . PageMeta::load()['defaultTitle'];
        $description = $post['subtitle'] ?? '';
        $metaDescription = $description !== '' ? $description : PageMeta::page('postDetail')['description'];
        $siteName = PageMeta::load()['siteName'];
        $ogImage = $post['image'] ? url($post['image']) : null;

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post['title'],
            'url' => $request->url(),
            'image' => $ogImage,
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteName,
            ],
            'datePublished' => $post['created_at'],
            'dateModified' => $post['updated_at'],
        ];

        return view('app', PageMeta::viewData($request, 'postDetail', $jsonLd, $postData, $title, $metaDescription, $ogImage));
    }
}