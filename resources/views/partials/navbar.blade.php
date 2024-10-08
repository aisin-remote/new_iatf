<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="{{ route('select.dashboard') }}"><img
                src="{{ asset('images/logodonna.png') }}" class="mr-2" alt="logo"
                style="width: 200px; height: auto;" /></a>
        <a class="navbar-brand brand-logo-mini" href="{{ route('select.dashboard') }}"><img
                src="{{ asset('images/logodonna.png') }}" alt="logo" /></a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="icon-menu"><i class="fa-solid fa-bars"></i></span>
        </button>

        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item dropdown">
                @include('partials.notifications')
            </li>

            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    {{ optional(\App\Models\Departemen::find(Auth::user()->departemen_id))->nama_departemen ?? 'No Department Assigned' }}
                    <i class="fa-solid fa-sort-down"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <a class="dropdown-item" href="{{ route('logout') }}">
                        <i class="ti-power-off text-primary"></i>
                        Logout
                    </a>
                </div>
            </li>

            <li class="nav-item nav-settings d-none d-lg-flex">
                <a class="nav-link" href="#">
                    <i class="icon-ellipsis"></i>
                </a>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-toggle="minimize">
            <span class="icon-menu"></span>
        </button>
    </div>
</nav>

@push('scripts')
    <script>
        $(document).ready(function() {
            var body = $("body");

            // Mendengarkan klik pada tombol dengan data-toggle="minimize"
            $('[data-toggle="minimize"]').on("click", function() {
                // Cek kondisi body untuk menampilkan atau menyembunyikan sidebar
                if (body.hasClass("sidebar-toggle-display") || body.hasClass("sidebar-absolute")) {
                    body.toggleClass("sidebar-hidden"); // Menyembunyikan sidebar
                } else {
                    body.toggleClass("sidebar-icon-only"); // Mengaktifkan mode icon-only
                }
            });
        });
    </script>
@endpush
