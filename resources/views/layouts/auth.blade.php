<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Kelola konten Portal Informasi Sarjana Informatika.">
    <title>@yield('title', 'Form') - Portal Informasi Sarjana Informatika</title>
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Portal IF" />
    <link rel="manifest" href="/site.webmanifest">
    <link href="/css/fonts/figtree.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/admin.css">
    <link href="/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
</head>

<body class="admin-auth">
    <main>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    @include('partials.alerts')
                    @yield('content')
                </div>
            </div>
        </div>
    </main>
</body>

</html>
