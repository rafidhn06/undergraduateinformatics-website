<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FeedbackLink;
use App\Services\MsForms\FormDefinitionService;
use App\Services\MsForms\MsFormsException;
use App\Support\PageMeta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function show(Request $request): View
    {
        $feedbackLink = FeedbackLink::configured()->first();

        $initialData = null;

        if ($feedbackLink) {
            try {
                $initialData = app(FormDefinitionService::class)->resolve('feedback');
            } catch (MsFormsException) {
                $initialData = null;
            }
        }

        if ($initialData === null) {
            $initialData = ['link' => null];
        }

        $page = PageMeta::page('feedback');

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $page['title'],
            'url' => $request->url(),
            'description' => $page['description'],
        ];

        return view('app', PageMeta::viewData($request, 'feedback', $jsonLd, [
            'status' => 'success',
            'data' => $initialData,
        ]));
    }
}
