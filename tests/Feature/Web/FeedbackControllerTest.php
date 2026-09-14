<?php

namespace Tests\Feature\Web;

use App\Models\FeedbackLink;
use App\Services\MsForms\FormDefinitionService;
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

    public function test_feedback_page_renders_spa_shell(): void
    {
        $response = $this->get('/feedback');

        $response->assertStatus(200);
        $response->assertViewIs('app');
        $response->assertSee('__INITIAL_DATA__', false);
    }

    public function test_feedback_page_injects_null_link_when_not_configured(): void
    {
        FeedbackLink::query()->delete();

        $response = $this->get('/feedback');

        $response->assertStatus(200);
        $response->assertSee('"link":null', false);
    }

    public function test_feedback_page_injects_null_link_when_no_definition_is_stored(): void
    {
        FeedbackLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $response = $this->get('/feedback');

        $response->assertStatus(200);
        $response->assertSee('"link":null', false);
    }

    public function test_feedback_page_injects_form_definition_when_configured(): void
    {
        FeedbackLink::create(['link' => 'https://forms.office.com/r/abc123']);
        app(FormDefinitionService::class)->refresh('feedback', 'https://forms.office.com/r/abc123');

        $response = $this->get('/feedback');

        $response->assertStatus(200);
        $response->assertSee('this is form title', false);
        $response->assertSee('"questions"', false);
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
