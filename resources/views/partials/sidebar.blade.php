<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard.rule') }}">
                <i class="fa-solid fa-house" style="margin-right: 8px"></i>
                <span class="menu-title"> Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('template.index') }}">
                <i class="fa-solid fa-file-contract" style="margin-right: 14px"></i>
                <span class="menu-title"> Template Dokumen</span>
            </a>
        </li>
        @role('guest')
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#rule" aria-expanded="false" aria-controls="rule">
                    <i class="fas fa-file" style="margin-right: 14px"></i>
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
        @endrole

        @role('admin')
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#validasi-draft-rule" aria-expanded="false"
                    aria-controls="validasi-draft-rule">
                    <i class="fas fa-file" style="margin-right: 14px"></i>
                    <span class="menu-title"> Validasi Draft Rule</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="validasi-draft-rule">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('rule.validate', ['jenis' => 'rule', 'tipe' => 'WI']) }}">WI</a>
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
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#tables" aria-expanded="false" aria-controls="tables">
                    <i class="fa-solid fa-file-circle-check" style="margin-right: 10px"></i>
                    <span class="menu-title"> Validasi Final</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="tables">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"> <a class="nav-link"
                                href="{{ route('final.validate', ['jenis' => 'rule', 'tipe' => 'WI']) }}">WI</a>
                        </li>
                        <li class="nav-item"> <a class="nav-link"
                                href="{{ route('final.validate', ['jenis' => 'rule', 'tipe' => 'Prosedur']) }}">Prosedur</a></li>
                        <li class="nav-item"> <a class="nav-link"
                                href="{{ route('final.validate', ['jenis' => 'rule', 'tipe' => 'Standar']) }}">Standar</a></li>
                        <li class="nav-item"> <a class="nav-link"
                                href="{{ route('final.validate', ['jenis' => 'rule', 'tipe' => 'WIS']) }}">WIS</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endrole
        <li class="nav-item">
            <a class="nav-link" href="{{ route('document.final') }}">
                <i class="fa-solid fa-file-contract" style="margin-right: 14px"></i>
                <span class="menu-title"> Final Dokumen</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('document.share') }}">
                <i class="fa-solid fa-file-contract" style="margin-right: 14px"></i>
                <span class="menu-title"> Shared Dokumen</span>
            </a>
        </li>
    </ul>
</nav>
