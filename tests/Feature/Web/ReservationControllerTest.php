<?php

namespace Tests\Feature\Web;

use App\Models\ReservationLink;
use App\Models\ReservationSchedule;
use App\Models\User;
use App\Services\MsForms\FormDefinitionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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

    public function test_reservations_index_and_put_update(): void
    {
        $admin = User::create([
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ]);
        $this->actingAs($admin);
        $schedule = ReservationSchedule::create(['requested_by' => 'Rafi', 'date' => '2026-10-01', 'shift' => 'pagi', 'agenda' => 'Rapat']);

        $this->put("/admin/reservations/{$schedule->id}", ['requested_by' => 'Rafi Baru', 'date' => '2026-10-01', 'shift' => 'pagi', 'agenda' => 'Rapat'])->assertRedirect();
    }

    public function test_store_surfaces_error_when_document_generation_fails(): void
    {
        $admin = User::create([
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ]);
        $this->actingAs($admin);
        Pdf::shouldReceive('loadView')->once()->andThrow(new \RuntimeException('dompdf exploded'));

        $response = $this->post('/admin/reservations', [
            'date' => '2026-10-01',
            'shift' => '09:00',
            'requested_by' => 'Rafi',
            'agenda' => 'Rapat',
        ]);

        $response->assertRedirect(route('admin.reservations.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('error');
        $this->assertNull(ReservationSchedule::first()->document_link);
    }

    public function test_reservation_page_injects_seo_metadata(): void
    {
        $response = $this->get('/reservation');

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

        $response->assertSee('"dateQuestionId"', false);
        $response->assertSee('"allowedDays"', false);
    }

    public function test_document_download_requires_authentication(): void
    {
        $schedule = ReservationSchedule::create([
            'requested_by' => 'Rafi',
            'date' => '2026-10-01',
            'shift' => 'pagi',
            'agenda' => 'Rapat',
        ]);

        $this->get("/admin/reservations/{$schedule->id}/document")->assertRedirect(route('admin.login'));
    }

    public function test_document_download_streams_private_file_for_admin(): void
    {
        Storage::fake('local');

        $admin = User::create([
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ]);
        $this->actingAs($admin);
        $schedule = ReservationSchedule::create([
            'requested_by' => 'Rafi',
            'date' => '2026-10-01',
            'shift' => 'pagi',
            'agenda' => 'Rapat',
        ]);
        Storage::disk('local')->put('beritaacara/berita_acara_' . $schedule->id . '.pdf', 'pdf-bytes');
        $schedule->update(['document_link' => route('admin.reservations.document', $schedule)]);

        $response = $this->get("/admin/reservations/{$schedule->id}/document");

        $response->assertOk();
        $this->assertSame('pdf-bytes', $response->streamedContent());
    }

    public function test_document_download_redirects_legacy_public_file(): void
    {
        $directory = public_path('beritaacara');
        if (! file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        file_put_contents($directory . '/berita_acara_legacy.pdf', 'pdf-bytes');

        try {
            $admin = User::create([
                'email' => fake()->unique()->safeEmail(),
                'password' => 'password',
            ]);
            $this->actingAs($admin);
            $schedule = ReservationSchedule::create([
                'requested_by' => 'Rafi',
                'date' => '2026-10-01',
                'shift' => 'pagi',
                'agenda' => 'Rapat',
                'document_link' => url('beritaacara/berita_acara_legacy.pdf'),
            ]);

            $this->get("/admin/reservations/{$schedule->id}/document")
                ->assertRedirect(url('beritaacara/berita_acara_legacy.pdf'));
        } finally {
            unlink($directory . '/berita_acara_legacy.pdf');
        }
    }

    public function test_document_download_returns_404_without_a_file(): void
    {
        $admin = User::create([
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ]);
        $this->actingAs($admin);
        $schedule = ReservationSchedule::create([
            'requested_by' => 'Rafi',
            'date' => '2026-10-01',
            'shift' => 'pagi',
            'agenda' => 'Rapat',
        ]);

        $this->get("/admin/reservations/{$schedule->id}/document")->assertNotFound();
    }
}