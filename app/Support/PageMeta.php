<?php

namespace App\Support;

use Illuminate\Http\Request;

final class PageMeta
{
    private static ?array $seo = null;

    private const FALLBACK = [
        'siteName' => 'Telkom University',
        'defaultTitle' => 'Portal Informasi Sarjana Informatika',
        'defaultDescription' => 'Sumber informasi resmi Program Studi Sarjana Informatika Telkom University untuk perkuliahan peserta didik.',
        'defaultOgImage' => '/images/banner.jpg',
        'ogType' => 'website',
        'pages' => [],
    ];

    public static function load(): array
    {
        if (self::$seo !== null) {
            return self::$seo;
        }

        $config = json_decode((string) file_get_contents(base_path('seo.json')), true);

        return self::$seo = is_array($config)
            ? array_merge(self::FALLBACK, $config)
            : self::FALLBACK;
    }

    public static function page(string $key): array
    {
        $seo = self::load();

        return array_merge([
            'title' => $seo['defaultTitle'],
            'description' => $seo['defaultDescription'],
        ], $seo['pages'][$key] ?? []);
    }

    public static function viewData(
        Request $request,
        string $page,
        array $jsonLd,
        array $initialData,
        ?string $title = null,
        ?string $description = null,
        ?string $ogImage = null
    ): array {
        $seo = self::load();
        $defaults = self::page($page);

        return [
            'title' => $title ?? $defaults['title'],
            'description' => $description ?? $defaults['description'],
            'siteName' => $seo['siteName'],
            'ogTitle' => $title ?? $defaults['title'],
            'ogDescription' => $description ?? $defaults['description'],
            'ogImage' => $ogImage ?? url($seo['defaultOgImage']),
            'ogType' => $seo['ogType'],
            'ogUrl' => $request->url(),
            'jsonLd' => $jsonLd,
            'initialData' => $initialData,
        ];
    }
}