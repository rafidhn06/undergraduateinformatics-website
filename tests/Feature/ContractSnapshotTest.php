<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_writes_endpoint_snapshots_to_path(): void
    {
        $tag = Tag::create(['name' => 'Rilis', 'description' => 'Kabar rilis']);
        $post = Post::create(['title' => 'Rilis Pertama', 'subtitle' => 'Sub', 'body' => 'Isi']);
        $post->tags()->attach($tag->id);

        $path = tempnam(sys_get_temp_dir(), 'snapshot').'.json';

        $this->artisan('contract:snapshot', ['--path' => $path])->assertSuccessful();

        $payload = json_decode((string) file_get_contents($path), true);

        $this->assertArrayHasKey('generated_at', $payload);
        $this->assertSame(
            ['GET /api/home', 'GET /api/posts/{slug}', 'GET /api/tags', 'GET /api/tags/{slug}', 'GET /api/links', 'GET /api/posts/search'],
            array_keys($payload['endpoints'])
        );
        $this->assertSame('success', $payload['endpoints']['GET /api/home']['status']);
        $this->assertSame($post->slug, $payload['endpoints']['GET /api/posts/{slug}']['data']['slug']);
        $this->assertSame($tag->slug, $payload['endpoints']['GET /api/tags/{slug}']['data']['slug']);

        unlink($path);
    }

    public function test_command_fails_without_content(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'snapshot').'.json';

        $this->artisan('contract:snapshot', ['--path' => $path])
            ->assertFailed()
            ->expectsOutputToContain('at least one post and one tag');
    }
}
