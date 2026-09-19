<aside class="admin-sidebar">
    <div class="admin-brand"></div>

    <nav class="admin-nav" aria-label="Navigasi admin">
        <p class="admin-nav__label">PENGATURAN UMUM</p>
        <a class="admin-nav__link {{ Route::is('admin.datasets*', 'admin.dataset-imports*') ? 'is-active' : '' }}" href="{{ route('admin.datasets.index') }}"><i class="fa-solid fa-chart-line"></i><span>Statistik Mahasiswa</span></a>
        <a class="admin-nav__link {{ Route::is('admin.form-links*') ? 'is-active' : '' }}" href="{{ route('admin.form-links.show') }}"><i class="fa-solid fa-link"></i><span>Manajemen Form Link</span></a>
        <a class="admin-nav__link {{ Route::is('admin.reservations*') ? 'is-active' : '' }}" href="{{ route('admin.reservations.index') }}"><i class="fa-solid fa-calendar-check"></i><span>Approval Reservasi</span></a>

        <p class="admin-nav__label">PENGATURAN POST INFORMASI</p>
        <a class="admin-nav__link {{ Route::is('admin.posts*') ? 'is-active' : '' }}" href="{{ route('admin.posts.index') }}"><i class="fa-solid fa-file-lines"></i><span>Manajemen Informasi</span></a>
        <a class="admin-nav__link {{ Route::is('admin.tags*') ? 'is-active' : '' }}" href="{{ route('admin.tags.index') }}"><i class="fa-solid fa-tag"></i><span>Tag Post Informasi</span></a>

        <p class="admin-nav__label">PENGATURAN LINK PENTING</p>
        <a class="admin-nav__link {{ Route::is('admin.sections*') ? 'is-active' : '' }}" href="{{ route('admin.sections.index') }}"><i class="fa-solid fa-list"></i><span>Section Link Penting</span></a>
        <a class="admin-nav__link {{ Route::is('admin.links*') ? 'is-active' : '' }}" href="{{ route('admin.links.index') }}"><i class="fa-solid fa-link"></i><span>Manajemen Link Penting</span></a>
    </nav>

    <div class="admin-sidebar__footer">
        <a class="admin-nav__link" href="{{ route('home') }}"><i class="fa-solid fa-arrow-left"></i><span>Kembali ke website</span></a>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="admin-nav__link"><i class="fa-solid fa-arrow-right-from-bracket"></i><span>Logout</span></button>
        </form>
    </div>
</aside>
