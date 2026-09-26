<?php

namespace Tests\Feature\Web;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageFileControllerTest extends TestCase
{
    public function test_serves_file_from_public_disk(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('posts/photo.jpg', 'fake-image-bytes');

        $response = $this->get('/storage/posts/photo.jpg');

        $response->assertStatus(200);
        $this->assertSame('fake-image-bytes', $response->streamedContent());
        $this->assertStringContainsString('immutable', (string) $response->headers->get('Cache-Control'));
    }

    public function test_returns_404_for_missing_file(): void
    {
        Storage::fake('public');

        $response = $this->get('/storage/posts/gone.jpg');

        $response->assertStatus(404);
    }

    public function test_returns_404_for_traversal_path(): void
    {
        Storage::fake('public');

        $response = $this->get('/storage/..%2F..%2F.env');

        $response->assertStatus(404);
    }
}
