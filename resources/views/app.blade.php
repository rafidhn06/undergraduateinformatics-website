<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-ssr="true">{{ $title ?? $seoDefaults['defaultTitle'] }}</title>
    <meta data-ssr="true" name="description" content="{{ $description ?? $seoDefaults['defaultDescription'] }}">
    <meta data-ssr="true" property="og:title" content="{{ $ogTitle ?? $title ?? $seoDefaults['defaultTitle'] }}">
    <meta data-ssr="true" property="og:description" content="{{ $ogDescription ?? $description ?? $seoDefaults['defaultDescription'] }}">
    <meta data-ssr="true" property="og:type" content="{{ $ogType ?? $seoDefaults['ogType'] }}">
    <meta data-ssr="true" property="og:site_name" content="{{ $siteName ?? $seoDefaults['siteName'] }}">
    <meta data-ssr="true" property="og:image" content="{{ $ogImage ?? url($seoDefaults['defaultOgImage']) }}">
    <meta data-ssr="true" property="og:url" content="{{ $ogUrl ?? url('/') }}">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Portal IF" />
    <link rel="manifest" href="/site.webmanifest" />
    @if(isset($jsonLd) && $jsonLd)
    <script type="application/ld+json">
        @json($jsonLd)
    </script>
    @endif
    <script>
        window.__INITIAL_DATA__ = @json($initialData ?? null);
    </script>
    <script>
        try {
            const storedTheme = localStorage.getItem('public-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (storedTheme === 'dark' || (!storedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        } catch (error) {}
    </script>
@viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
    <link rel="stylesheet" href="/css/initial-loader.css">
</head>
<body>
    <div id="root">
        @include('partials.initial-loader')
    </div>
</body>
</html>
