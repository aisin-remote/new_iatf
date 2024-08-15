<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="{{ route('dashboard.rule') }}"><img
                src="{{ asset('images/logo-iatf.png') }}" class="mr-2" alt="logo"
                style="width: 80px; height: auto;" /></a>
        <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard.rule') }}"><img
                src="{{ asset('images/logo-iatf-mini.png') }}" alt="logo" /></a>
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
                    {{ Auth::user()->selectedDepartment ? Auth::user()->selectedDepartment->nama_departemen : 'No Department Assigned' }}
                    <i class="fa-solid fa-sort-down"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <!-- Formulir untuk mengganti departemen -->
                    @if (Auth::user()->departments && Auth::user()->departments->count() > 0)
                        <form action="{{ route('home.switch.departemen') }}" method="POST">
                            @csrf
                            <div class="dropdown-item">
                                <select name="department_id" class="form-control" onchange="this.form.submit()">
                                    @foreach (Auth::user()->departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ $department->id == Auth::user()->selectedDepartment?->id ? 'selected' : '' }}>
                                            {{ $department->nama_departemen }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    @else
                        <div class="dropdown-item">
                            No Departments Available
                        </div>
                    @endif

                    <div class="dropdown-divider"></div>
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
            data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>
    </div>
</nav>
