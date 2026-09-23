<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Posts\PostListQuery;
use App\Services\Posts\PostsDataService;
use App\Support\ApiResponse;
use App\Support\NotFoundResponse;
use App\Support\PageMeta;
use App\Support\PageSeed;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(
        private readonly PostsDataService $posts,
    ) {
    }

    public function index(Request $request): View
    {
        $query = PostListQuery::fromArray($request->query());
        $payload = $this->posts->resolveList($query);
        $params = array_filter([
            'q' => $query->q,
            'page' => $query->page,
            'per_page' => $query->perPage,
        ], fn ($value) => $value !== null && $value !== '');

        return view('app', PageMeta::viewData($request, 'postSearch', [], [
            PageSeed::entry('/api/posts', $payload, $params),
        ]));
    }

    public function show(Request $request, string $slugOrId): View|Response
    {
        try {
            $post = $this->posts->resolveDetailPayload($slugOrId);
        } catch (ModelNotFoundException) {
            return NotFoundResponse::view($request);
        }

        $postData = ApiResponse::success($post);

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

        return view('app', PageMeta::viewData($request, 'postDetail', $jsonLd, [
            PageSeed::entry('/api/posts/'.$slugOrId, $postData),
        ], $title, $metaDescription, $ogImage));
    }
}