<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardDatasetResource;
use App\Http\Resources\ImportantLinkResource;
use App\Models\DashboardDataset;
use App\Services\ImportantLinks\ImportantLinkQuery;
use App\Services\Search\SearchDataService;
use App\Support\PageMeta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $posts = app(SearchDataService::class)->resolve(null, 1, 5);
        $links = app(ImportantLinkQuery::class)->latest(1, 5);
        $datasets = DashboardDataset::query()->with('items')->orderBy('id')->get();
        $initialData = [
            'posts' => $posts['data'] ?? [],
            'links' => ImportantLinkResource::collection($links)->resolve(),
            'datasets' => DashboardDatasetResource::collection($datasets)->resolve(),
        ];
        $seo = PageMeta::page('home');
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => PageMeta::load()['siteName'],
            'url' => $request->url(),
            'description' => $seo['description'],
        ];

        return view('app', PageMeta::viewData($request, 'home', $jsonLd, $initialData));
    }
}
