<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Kelola konten Portal Informasi Sarjana Informatika.">
    <title>@yield('title', 'Admin') - Portal Informasi Sarjana Informatika</title>
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Portal IF" />
    <link rel="manifest" href="/site.webmanifest">
    <link href="/css/fonts/figtree.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/admin.css">
    <script src="/vendor/ckeditor/ckeditor.js"></script>
    <script src="/vendor/ckeditor/translations/id.js"></script>
    <link href="/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
</head>

<body class="admin-app">
    <div class="admin-shell">
        @include('navbars.admin')

        <div class="admin-sidebar-overlay" id="admin-sidebar-overlay" aria-hidden="true"></div>

        <div class="admin-main">
            <header class="admin-header">
                <div class="admin-header__left">
                    <button class="admin-sidebar-toggle" id="admin-sidebar-toggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
            </header>

            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>
    <script>
        (function () {
            const toggle = document.getElementById('admin-sidebar-toggle');
            const sidebar = document.querySelector('.admin-sidebar');
            const overlay = document.getElementById('admin-sidebar-overlay');
            if (!toggle || !sidebar) return;

            const isMobile = () => window.matchMedia('(max-width: 1023.98px)').matches;

            function openSidebar() {
                sidebar.classList.add('is-open');
                overlay.classList.add('is-visible');
                document.body.classList.add('sidebar-locked');
                toggle.setAttribute('aria-expanded', 'true');
            }

            function closeSidebar() {
                sidebar.classList.remove('is-open');
                overlay.classList.remove('is-visible');
                document.body.classList.remove('sidebar-locked');
                toggle.setAttribute('aria-expanded', 'false');
            }

            toggle.addEventListener('click', () => (sidebar.classList.contains('is-open') ? closeSidebar() : openSidebar()));
            overlay.addEventListener('click', closeSidebar);
            document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeSidebar(); });
            document.querySelectorAll('.admin-nav__link').forEach((link) => link.addEventListener('click', () => { if (isMobile()) closeSidebar(); }));
        })();
    </script>
    @stack('scripts')
</body>

</html>
