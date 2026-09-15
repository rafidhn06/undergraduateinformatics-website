<?php

namespace Tests\Feature\Web;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\FakesMicrosoftForms;
use Tests\TestCase;

class FeedbackControllerTest extends TestCase
{
    use FakesMicrosoftForms;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        Http::fake($this->microsoftEndpoints());
    }

    public function test_feedback_page_injects_seo_metadata(): void
    {
        $response = $this->get('/feedback');

        $response->assertStatus(200);
        $response->assertSee('Masukan - Portal Informasi Sarjana Informatika', false);
        $response->assertSee('property="og:title"', false);
        $response->assertSee('property="og:description"', false);
        $response->assertSee('application/ld+json', false);
        $response->assertSee('Sampaikan pengaduan, keluhan, atau aspirasi terkait layanan akademik maupun non-akademik Program Studi Sarjana Informatika Telkom University', false);
    }
}
