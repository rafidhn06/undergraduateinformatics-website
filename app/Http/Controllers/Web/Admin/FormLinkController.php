<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackLink;
use App\Models\MsFormDefinition;
use App\Models\ReservationLink;
use App\Services\MsForms\FormDefinitionService;
use App\Services\MsForms\MsFormsException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class FormLinkController extends Controller
{
    public function show(): View
    {
        return view('AdminDashboard.feedback', [
            'feedbackLink' => FeedbackLink::query()->first(),
            'reservationLink' => Schema::hasTable('reservation_links') ? ReservationLink::query()->first() : null,
            'feedbackDefinition' => MsFormDefinition::query()->where('kind', 'feedback')->first(),
            'reservationDefinition' => MsFormDefinition::query()->where('kind', 'reservation')->first(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'feedback_link' => ['nullable', 'url'],
            'reservation_link' => ['nullable', 'url'],
        ]);

        $message = 'Link berhasil diperbarui.';

        if (isset($validated['feedback_link'])) {
            $feedbackLink = FeedbackLink::query()->firstOrCreate([], ['link' => '']);
            $feedbackLink->update(['link' => $validated['feedback_link']]);
            try {
                app(FormDefinitionService::class)->refresh('feedback', $validated['feedback_link']);
            } catch (MsFormsException) {
                return redirect()->route('admin.form-links.show')->with('warning', 'Link tersimpan, tetapi refresh definisi gagal. Coba lagi.');
            }
            $feedbackDefinition = MsFormDefinition::query()->where('kind', 'feedback')->first();
            if ($feedbackDefinition && $feedbackDefinition->fetched_at) {
                $message .= ' Definisi di-refresh pada ' . $feedbackDefinition->fetched_at->format('d M Y H:i') . '.';
            }
        }

        if (isset($validated['reservation_link'])) {
            $reservationLink = ReservationLink::query()->firstOrCreate([], ['link' => '']);
            $reservationLink->update(['link' => $validated['reservation_link']]);
            try {
                app(FormDefinitionService::class)->refresh('reservation', $validated['reservation_link']);
            } catch (MsFormsException) {
                return redirect()->route('admin.form-links.show')->with('warning', 'Link tersimpan, tetapi refresh definisi gagal. Coba lagi.');
            }
            $reservationDefinition = MsFormDefinition::query()->where('kind', 'reservation')->first();
            if ($reservationDefinition && $reservationDefinition->fetched_at) {
                $message .= ' Definisi di-refresh pada ' . $reservationDefinition->fetched_at->format('d M Y H:i') . '.';
            }
        }

        return redirect()->route('admin.form-links.show')->with('success', $message);
    }
}
