<?php

namespace App\Console\Commands;

use App\Http\Resources\DashboardDatasetResource;
use App\Http\Resources\ImportantLinkResource;
use App\Http\Resources\ImportantSectionResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostSummaryResource;
use App\Http\Resources\TagResource;
use App\Models\DashboardDataset;
use App\Models\ImportantLink;
use App\Models\ImportantSection;
use App\Models\Post;
use App\Models\Tag;
use App\Services\Posts\PostsDataService;
use App\Services\Search\SearchDataService;
use App\Services\Tags\TagsDataService;
use App\Support\ApiResponse;
use App\Support\Pagination;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;

final class ContractSnapshot extends Command
{
    protected $signature = 'contract:snapshot {--path=} {--post=} {--tag=} {--search=}';

    protected $description = 'Snapshot public API response shapes for the static frontend contract';

    public function handle(
        PostsDataService $posts,
        TagsDataService $tags,
        SearchDataService $search
    ): int {
        try {
            $postSlug = (string) ($this->option('post') ?: Post::query()->orderBy('id')->firstOrFail()->slug);
            $tagSlug = (string) ($this->option('tag') ?: Tag::query()->orderBy('id')->firstOrFail()->slug);
        } catch (ModelNotFoundException) {
            $this->error('Snapshot needs at least one post and one tag in the database.');

            return self::FAILURE;
        }

        $searchPosts = $search->resolve(null, 1, 2);

        $payload = [
            'generated_at' => now()->toIso8601String(),
            'endpoints' => [
                'GET /api/posts' => ApiResponse::success(
                    PostSummaryResource::collection($searchPosts)->resolve(),
                    Pagination::meta($searchPosts),
                ),
                'GET /api/posts/{slug}' => ApiResponse::success(
                    PostResource::make($posts->resolveDetail($postSlug))->resolve()
                ),
                'GET /api/tags' => $tags->resolveListPayload(),
                'GET /api/tags/{slug}' => ApiResponse::success(
                    TagResource::make($tags->resolveDetail($tagSlug))->resolve()
                ),
                'GET /api/link-sections' => ApiResponse::success(
                    ImportantSectionResource::collection(ImportantSection::orderedWithLinks()->get())->resolve()
                ),
                'GET /api/important-links' => ApiResponse::success(
                    ImportantLinkResource::collection(ImportantLink::latestPage(1, 2))->resolve()
                ),
                'GET /api/datasets' => ApiResponse::success(
                    DashboardDatasetResource::collection(DashboardDataset::query()->with(['items' => fn ($query) => $query->orderBy('sort_order')])->orderBy('id')->get())->resolve()
                ),
            ],
        ];

        $path = (string) ($this->option('path') ?: storage_path('app/contract/openapi.snapshot.json'));

        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $this->info('Contract snapshot written to '.$path);

        return self::SUCCESS;
    }
}
