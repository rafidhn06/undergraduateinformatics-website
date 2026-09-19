<?php

namespace App\Console\Commands;

use App\Http\Resources\DashboardDatasetResource;
use App\Http\Resources\ImportantLinkResource;
use App\Models\DashboardDataset;
use App\Models\Post;
use App\Models\Tag;
use App\Services\ImportantLinks\ImportantLinkQuery;
use App\Services\Links\LinksDataService;
use App\Services\Posts\PostsDataService;
use App\Services\Search\SearchDataService;
use App\Services\Tags\TagsDataService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class ContractSnapshot extends Command
{
    protected $signature = 'contract:snapshot {--path=} {--post=} {--tag=} {--search=}';

    protected $description = 'Snapshot public API response shapes for the static frontend contract';

    public function handle(
        PostsDataService $posts,
        TagsDataService $tags,
        LinksDataService $links,
        SearchDataService $search,
        ImportantLinkQuery $importantLinks
    ): int {
        try {
            $postSlug = (string) ($this->option('post') ?: Post::query()->orderBy('id')->firstOrFail()->slug);
            $tagSlug = (string) ($this->option('tag') ?: Tag::query()->orderBy('id')->firstOrFail()->slug);
        } catch (ModelNotFoundException) {
            $this->error('Snapshot needs at least one post and one tag in the database.');

            return self::FAILURE;
        }

        $payload = [
            'generated_at' => now()->toIso8601String(),
            'endpoints' => [
                'GET /api/posts' => $search->resolve(null, 1, 2),
                'GET /api/posts/{slug}' => $posts->resolveDetail($postSlug),
                'GET /api/tags' => $tags->resolve(),
                'GET /api/tags/{slug}' => $tags->resolveDetail($tagSlug),
                'GET /api/link-sections' => $links->getSectionsWithLinks(),
                'GET /api/important-links' => [
                    'status' => 'success',
                    'data' => ImportantLinkResource::collection($importantLinks->latest(1, 2))->resolve(),
                ],
                'GET /api/datasets' => [
                    'status' => 'success',
                    'data' => DashboardDatasetResource::collection(DashboardDataset::query()->with('items')->orderBy('id')->get())->resolve(),
                ],
            ],
        ];

        $path = (string) ($this->option('path') ?: storage_path('app/contract/openapi.snapshot.json'));

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents(
            $path,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        $this->info('Contract snapshot written to '.$path);

        return self::SUCCESS;
    }
}
