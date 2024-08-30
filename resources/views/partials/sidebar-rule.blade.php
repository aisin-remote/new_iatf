<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard.rule') }}">
                <i class="fa-solid fa-house" style="margin-right: 8px"></i>
                <span class="menu-title"> Dashboard</span>
            </a>
        </li>
        @role('admin')
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#masterdata"
                    aria-expanded="{{ request()->routeIs('masterdata.*') ? 'true' : 'false' }}" aria-controls="masterdata">
                    <i class="fa-solid fa-database" style="margin-right: 12px"></i>
                    <span class="menu-title"> Master Data</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('masterdata.*') ? 'show' : '' }}" id="masterdata">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('masterdata.departemen') }}">Departemen</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('masterdata.kodeproses') }}">Rule Code</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('masterdata.role') }}">Role</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endrole

        <li class="nav-item">
            <a class="nav-link" href="{{ route('template.index') }}">
                <i class="fa-solid fa-file-pen" style="margin-right: 8px"></i>
                <span class="menu-title"> Template Documents</span>
            </a>
        </li>
        @role('guest')
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#rule-collapse-guest"
                    aria-expanded="{{ request()->is('rule.index') ? 'true' : 'false' }}"
                    aria-controls="rule-collapse-guest">
                    <i class="fa-solid fa-file-word" style="margin-right: 14px"></i>
                    <span class="menu-title"> Create/Revision Doc</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('rule.index') ? 'show' : '' }}" id="rule-collapse-guest">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('rule.index', ['jenis' => 'rule', 'tipe' => 'WI']) }}">WI</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('rule.index', ['jenis' => 'rule', 'tipe' => 'PROSEDUR']) }}">Procedure</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('rule.index', ['jenis' => 'rule', 'tipe' => 'STANDAR']) }}">Standard</a>
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
                <a class="nav-link" data-toggle="collapse" href="#validasi-draft-rule-collapse-admin"
                    aria-expanded="{{ request()->routeIs('rule.validate') ? 'true' : 'false' }}"
                    aria-controls="validasi-draft-rule-collapse-admin">
                    <i class="fa solid fa-file-circle-check" style="margin-right: 12px"></i>
                    <span class="menu-title"> Document Validation</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('rule.validate') ? 'show' : '' }}"
                    id="validasi-draft-rule-collapse-admin">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('rule.validate', ['jenis' => 'rule', 'tipe' => 'WI']) }}">WI</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('rule.validate', ['jenis' => 'rule', 'tipe' => 'PROSEDUR']) }}">Procedure</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('rule.validate', ['jenis' => 'rule', 'tipe' => 'STANDAR']) }}">Standard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('rule.validate', ['jenis' => 'rule', 'tipe' => 'WIS']) }}">WIS</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endrole
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#final-rule-collapse"
                aria-expanded="{{ request()->routeIs('documents.final') ? 'true' : 'false' }}"
                aria-controls="final-rule-collapse">
                <i class="fa-solid fa-file-pdf" style="margin-right: 14px"></i>
                <span class="menu-title"> Final Dokumen</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('documents.final') ? 'show' : '' }}" id="final-rule-collapse">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('documents.final', ['jenis' => 'rule', 'tipe' => 'WI']) }}">WI</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('documents.final', ['jenis' => 'rule', 'tipe' => 'Prosedur']) }}">Procedure</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('documents.final', ['jenis' => 'rule', 'tipe' => 'Standar']) }}">Standard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('documents.final', ['jenis' => 'rule', 'tipe' => 'WIS']) }}">WIS</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#document-share-collapse"
                aria-expanded="{{ request()->routeIs('document.share') ? 'true' : 'false' }}"
                aria-controls="document-share-collapse">
                <i class="fa-solid fa-file-import" style="margin-right: 14px"></i>
                <span class="menu-title"> Distributed Document</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('document.share') ? 'show' : '' }}"
                id="document-share-collapse">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('document.share', ['jenis' => 'rule', 'tipe' => 'WI']) }}">WI</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('document.share', ['jenis' => 'rule', 'tipe' => 'PROSEDUR']) }}">Procedure</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('document.share', ['jenis' => 'rule', 'tipe' => 'STANDAR']) }}">Standard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('document.share', ['jenis' => 'rule', 'tipe' => 'WIS']) }}">WIS</a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>
