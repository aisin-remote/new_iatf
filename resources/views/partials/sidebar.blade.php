<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard.rule') }}">
                <i class="fa-solid fa-house" style="margin-right: 8px"></i>
                <span class="menu-title"> Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#rule" aria-expanded="false" aria-controls="rule"><i
                    class="fas fa-file" style="margin-right: 14px"></i>
                <span class="menu-title"> Doc Rule</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="rule">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('rule.index', ['jenis' => 'rule', 'tipe' => 'WI']) }}">WI</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('rule.index', ['jenis' => 'rule', 'tipe' => 'Prosedur']) }}">Prosedur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('rule.index', ['jenis' => 'rule', 'tipe' => 'Standar']) }}">Standar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('rule.index', ['jenis' => 'rule', 'tipe' => 'WIS']) }}">WIS</a>
                    </li>
                </ul>

            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#proses" aria-expanded="false" aria-controls="proses"><i
                    class="fa-solid fa-file-lines" style="margin-right: 14px"></i>
                <span class="menu-title"> Doc Proses</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="proses">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="pages/charts/chartjs.html">QCPC</a></li>
                    <li class="nav-item"> <a class="nav-link" href="pages/charts/chartjs.html">FMEA</a></li>
                    <li class="nav-item"> <a class="nav-link" href="pages/charts/chartjs.html">DLL</a></li>
                    <li class="nav-item"> <a class="nav-link" href="pages/charts/chartjs.html">DLL</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#validasi-rule" aria-expanded="false"
                aria-controls="validasi-rule"><i class="fas fa-file" style="margin-right: 14px"></i>
                <span class="menu-title"> Validasi Rule</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="validasi-rule">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('rule.validate', ['jenis' => 'rule', 'tipe' => 'WI']) }}">WI</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('rule.validate', ['jenis' => 'rule', 'tipe' => 'Prosedur']) }}">Prosedur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('rule.validate', ['jenis' => 'rule', 'tipe' => 'Standar']) }}">Standar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('rule.validate', ['jenis' => 'rule', 'tipe' => 'WIS']) }}">WIS</a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>
