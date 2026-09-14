<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackLink;
use App\Models\MsFormDefinition;
use App\Models\ReservationLink;
use App\Services\MsForms\FormDefinitionService;
use App\Services\MsForms\MsFormsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class FormLinkController extends Controller
{
    public function show()
    {
        return view('AdminDashboard.feedback', [
            'feedbackLink' => FeedbackLink::query()->first(),
            'reservationLink' => Schema::hasTable('reservation_links')
                ? ReservationLink::query()->first()
                : null,
            'feedbackDefinition' => MsFormDefinition::query()->where('kind', 'feedback')->first(),
            'reservationDefinition' => MsFormDefinition::query()->where('kind', 'reservation')->first(),
        ]);
    }

    public function updateFeedback(Request $request)
    {
        $validated = $request->validate(['feedback_link' => ['required', 'url']]);
        $feedbackLink = FeedbackLink::query()->firstOrCreate([], ['link' => '']);
        $feedbackLink->update(['link' => $validated['feedback_link']]);

        return $this->refreshDefinition('feedback', $validated['feedback_link'], 'Link feedback berhasil diperbarui.');
    }

    public function updateReservation(Request $request)
    {
        $validated = $request->validate(['reservation_link' => ['required', 'url']]);
        $reservationLink = ReservationLink::query()->firstOrCreate([], ['link' => '']);
        $reservationLink->update(['link' => $validated['reservation_link']]);

        return $this->refreshDefinition('reservation', $validated['reservation_link'], 'Link reservasi berhasil diperbarui.');
    }

    public function refresh(string $kind)
    {
        abort_unless(in_array($kind, ['feedback', 'reservation'], true), 404);

        return $this->refreshDefinition($kind, null, 'Definisi berhasil di-refresh.');
    }

    private function refreshDefinition(string $kind, ?string $link, string $successMessage)
    {
        try {
            app(FormDefinitionService::class)->refresh($kind, $link);
        } catch (MsFormsException) {
            return redirect()->route('admin.form-link')->with('warning', 'Link tersimpan, tetapi refresh definisi gagal. Coba lagi.');
        }

        $fetchedAt = MsFormDefinition::query()->where('kind', $kind)->first()?->fetched_at;

        return redirect()->route('admin.form-link')->with(
            'success',
            $successMessage . ($fetchedAt ? ' Definisi di-refresh pada ' . $fetchedAt->format('d M Y H:i') . '.' : '')
        );
    }
}
