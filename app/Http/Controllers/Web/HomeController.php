<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardDatasetResource;
use App\Http\Resources\ImportantLinkResource;
use App\Http\Resources\PostSummaryResource;
use App\Models\DashboardDataset;
use App\Models\ImportantLink;
use App\Services\Search\SearchDataService;
use App\Support\ApiResponse;
use App\Support\PageMeta;
use App\Support\PageSeed;
use App\Support\Pagination;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly SearchDataService $search,
    ) {
    }

    public function index(Request $request): View
    {
        $posts = $this->search->resolve(null, 1, 5);
        $links = ImportantLink::latestPage(1, 5);
        $datasets = DashboardDataset::query()->with(['items' => fn ($query) => $query->orderBy('sort_order')])->orderBy('id')->get();
        $postsPayload = ApiResponse::success(
            PostSummaryResource::collection($posts)->resolve(),
            Pagination::meta($posts)
        );
        $linksPayload = ApiResponse::success(
            ImportantLinkResource::collection($links)->resolve(),
            Pagination::meta($links)
        );
        $datasetsPayload = [
            'status' => 'success',
            'data' => DashboardDatasetResource::collection($datasets)->resolve(),
        ];
        $seeds = [
            PageSeed::entry('/api/posts', $postsPayload, ['per_page' => 5]),
            PageSeed::entry('/api/important-links', $linksPayload, ['per_page' => 5]),
            PageSeed::entry('/api/datasets', $datasetsPayload),
        ];
        $seo = PageMeta::page('home');
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => PageMeta::load()['siteName'],
            'url' => $request->url(),
            'description' => $seo['description'],
        ];

        return view('app', PageMeta::viewData($request, 'home', $jsonLd, $seeds));
    }
}
