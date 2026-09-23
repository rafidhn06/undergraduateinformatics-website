<?php

namespace Tests\Feature\Services\Reservation;

use App\Models\ReservationSchedule;
use App\Services\Reservation\BeritaAcaraPdfGenerator;
use App\Services\Reservation\ReservationDocumentException;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class BeritaAcaraPdfGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_writes_a_pdf_to_private_storage_at_a_deterministic_path(): void
    {
        Storage::fake('local');

        $schedule = ReservationSchedule::create([
            'date' => '2026-09-10',
            'shift' => '09:00:00',
            'requested_by' => 'Budi',
        ]);

        $fakePdf = Mockery::mock(DomPdf::class);
        $fakePdf->shouldReceive('output')->once()->andReturn('pdf-bytes');
        Pdf::shouldReceive('loadView')
            ->once()
            ->with('pdf.berita_acara', Mockery::on(
                fn ($data) => $data['schedule'] === $schedule && is_string($data['bgBase64'] ?? null)
            ))
            ->andReturn($fakePdf);

        $path = app(BeritaAcaraPdfGenerator::class)->generate($schedule);

        $this->assertSame('beritaacara/berita_acara_' . $schedule->id . '.pdf', $path);
        Storage::disk('local')->assertExists($path);
        $this->assertSame('pdf-bytes', Storage::disk('local')->get($path));
        $leftovers = array_filter(
            Storage::disk('local')->files('beritaacara'),
            fn ($file) => str_starts_with(basename($file), 'tmp_')
        );
        $this->assertSame([], array_values($leftovers));
    }

    public function test_it_throws_when_pdf_generation_fails(): void
    {
        $schedule = ReservationSchedule::create([
            'date' => '2026-09-10',
            'shift' => '09:00:00',
            'requested_by' => 'Budi',
        ]);

        Pdf::shouldReceive('loadView')->once()->andThrow(new \RuntimeException('dompdf exploded'));

        $this->expectException(ReservationDocumentException::class);

        app(BeritaAcaraPdfGenerator::class)->generate($schedule);
    }
}