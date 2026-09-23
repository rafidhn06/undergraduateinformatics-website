@if ($errors->any())
    <div class="alert alert-danger admin-toast" role="alert">
        <button type="button" class="btn-close admin-toast__close" aria-label="Tutup"></button>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session()->has('success'))
    <div class="alert alert-success admin-toast" role="alert">
        <button type="button" class="btn-close admin-toast__close" aria-label="Tutup"></button>
        {{ session('success') }}
    </div>
@endif

@if (session()->has('error'))
    <div class="alert alert-danger admin-toast" role="alert">
        <button type="button" class="btn-close admin-toast__close" aria-label="Tutup"></button>
        {{ session('error') }}
    </div>
@endif

@if ($errors->any() || session()->has('success') || session()->has('error'))
    <script>
        document.querySelectorAll('.admin-toast').forEach((toast) => {
            const dismiss = () => {
                toast.classList.add('admin-toast--hidden');
                window.setTimeout(() => toast.remove(), 250);
            };
            const timer = window.setTimeout(dismiss, 4000);
            toast.querySelector('.admin-toast__close')?.addEventListener('click', () => {
                window.clearTimeout(timer);
                dismiss();
            });
        });
    </script>
@endif
