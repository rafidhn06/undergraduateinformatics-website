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

    public function test_reservation_page_renders_spa_shell(): void
    {
        $response = $this->get('/reservation');

        $response->assertStatus(200);
        $response->assertViewIs('app');
        $response->assertSee('__INITIAL_DATA__', false);
    }

    public function test_reservation_page_injects_null_link_when_not_configured(): void
    {
        ReservationLink::query()->delete();

        $response = $this->get('/reservation');

        $response->assertStatus(200);
        $response->assertSee('"link":null', false);
    }

    public function test_reservation_page_auto_refreshes_when_no_definition_is_stored(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $response = $this->get('/reservation');

        $response->assertStatus(200);
        $response->assertSee('Reservation Form', false);
    }

    public function test_reservation_page_injects_form_definition_when_configured(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/r/abc123']);
        app(FormDefinitionService::class)->refresh('reservation', 'https://forms.office.com/r/abc123');

        $response = $this->get('/reservation');

        $response->assertStatus(200);
        $response->assertSee('Reservation Form', false);
        $response->assertSee('"questions"', false);
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