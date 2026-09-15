<?php

namespace Tests\Feature\Web;

use App\Models\ReservationLink;
use App\Services\MsForms\FormDefinitionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\FakesMicrosoftForms;
use Tests\TestCase;

class ReservationControllerTest extends TestCase
{
    use FakesMicrosoftForms;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->runtimeFixture = 'reservation-form.raw.json';

        Http::preventStrayRequests();
        Http::fake($this->microsoftEndpoints());
    }

    public function test_reservation_page_injects_seo_metadata(): void
    {
        $response = $this->get('/reservation');

        $response->assertStatus(200);
        $response->assertSee('Reservasi - Portal Informasi Sarjana Informatika', false);
        $response->assertSee('property="og:title"', false);
        $response->assertSee('property="og:description"', false);
        $response->assertSee('application/ld+json', false);
        $response->assertSee('Ajukan reservasi pertemuan dengan Program Studi Sarjana Informatika Telkom University', false);
    }

    public function test_reservation_page_injects_reservation_metadata(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);
        app(FormDefinitionService::class)->refresh('reservation', 'https://forms.office.com/r/abc123');

        $response = $this->get('/reservation');

        $response->assertStatus(200);
        $response->assertSee('"dateQuestionId"', false);
        $response->assertSee('"allowedDays"', false);
    }
}