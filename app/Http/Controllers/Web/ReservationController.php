<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\MsForms\MsFormsException;
use App\Services\Reservation\ReservationFormService;
use App\Services\Reservation\ReservationFormUnavailableException;
use App\Services\Reservation\ReservationMetadata;
use App\Support\PageMeta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function show(Request $request): View
    {
        $initialData = null;

        try {
            $initialData = app(ReservationFormService::class)->resolve();
        } catch (ReservationFormUnavailableException) {
            $initialData = ['link' => null];
        } catch (MsFormsException) {
            $initialData = null;
        }

        if ($initialData === null) {
            $initialData = ['link' => null];
        }

        $initialData['reservation'] = app(ReservationMetadata::class)->build();

        $page = PageMeta::page('reservation');

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $page['title'],
            'url' => $request->url(),
            'description' => $page['description'],
        ];

        return view('app', PageMeta::viewData($request, 'reservation', $jsonLd, [
            'status' => 'success',
            'data' => $initialData,
        ]));
    }
}