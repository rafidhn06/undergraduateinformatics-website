<?php

namespace App\Services\Reservation;

use App\Models\ReservationSchedule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaAcaraPdfGenerator
{
    public function generate(ReservationSchedule $schedule): string
    {
        try {
            $pdf = Pdf::loadView('pdf.berita_acara', [
                'schedule' => $schedule,
                'bgBase64' => $this->backgroundImage(),
            ]);

            $path = 'beritaacara/berita_acara_' . $schedule->id . '.pdf';
            $tempPath = 'beritaacara/tmp_' . Str::random(20) . '.pdf';
            Storage::disk('local')->put($tempPath, $pdf->output());
            Storage::disk('local')->move($tempPath, $path);

            return $path;
        } catch (\Throwable $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());

            throw new ReservationDocumentException('Dokumen berita acara gagal dibuat.', 0, $e);
        }
    }

    private function backgroundImage(): string
    {
        $bgPath = public_path('images/beritaacara/bg-docs.png');

        if (! is_file($bgPath) || ! is_readable($bgPath)) {
            return '';
        }

        $contents = file_get_contents($bgPath);

        if ($contents === false) {
            return '';
        }

        return 'data:image/' . pathinfo($bgPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($contents);
    }
}