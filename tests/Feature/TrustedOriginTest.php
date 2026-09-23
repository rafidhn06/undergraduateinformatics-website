<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrustedOriginTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_allows_requests_without_origin_headers(): void
    {
        $this->getJson('/api/posts')->assertOk();
    }

    public function test_api_rejects_untrusted_origin(): void
    {
        $this->getJson('/api/posts', ['Origin' => 'https://evil.example'])->assertForbidden();
    }

    public function test_api_rejects_untrusted_referer_when_origin_is_absent(): void
    {
        $this->getJson('/api/posts', ['Referer' => 'https://evil.example/page'])->assertForbidden();
    }

    public function test_api_rejects_loopback_origin_outside_local_environment(): void
    {
        $this->getJson('/api/posts', ['Origin' => 'http://127.0.0.1:3000'])->assertForbidden();
    }

    public function test_api_accepts_same_origin_request(): void
    {
        $this->getJson('/api/posts', ['Origin' => 'http://localhost:8000'])
            ->assertStatus(200);
    }

    public function test_api_rejects_foreign_origin_matching_configured_host_pattern(): void
    {
        $this->getJson('/api/posts', ['Origin' => 'https://localhost:9999'])
            ->assertStatus(200);
    }
}
