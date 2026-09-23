<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\MsForms\FormDefinitionService;
use App\Services\MsForms\MsFormsException;
use App\Support\PageMeta;
use App\Support\PageSeed;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function __construct(private readonly FormDefinitionService $forms)
    {
    }

    public function show(Request $request): View
    {
        $initialData = null;

        try {
            $initialData = $this->forms->resolve('feedback');
        } catch (MsFormsException) {
            $initialData = null;
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
            PageSeed::entry('/api/feedback-form', [
                'status' => 'success',
                'data' => $initialData,
            ]),
        ]));
    }
}
