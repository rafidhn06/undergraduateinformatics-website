<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\MsForms\MsFormsException;
use App\Services\Reservation\ReservationIntake;
use App\Support\PageMeta;
use App\Support\PageSeed;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(
        private readonly ReservationIntake $intake,
    ) {
    }

    public function show(Request $request): View
    {
        $initialData = null;

        try {
            $initialData = $this->intake->formPayload();
        } catch (MsFormsException) {
            $initialData = null;
        }

        if ($initialData === null) {
            $initialData = ['link' => null];
        }

        $initialData['reservation'] ??= $this->intake->reservationMetadata();

        $page = PageMeta::page('reservation');

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $page['title'],
            'url' => $request->url(),
            'description' => $page['description'],
        ];

        return view('app', PageMeta::viewData($request, 'reservation', $jsonLd, [
            PageSeed::entry('/api/reservation-form', [
                'status' => 'success',
                'data' => $initialData,
            ]),
        ]));
    }
}