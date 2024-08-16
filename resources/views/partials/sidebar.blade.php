<!-- resources/views/partials/sidebar.blade.php -->
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
                <a class="nav-link" href="{{ route('masterdata') }}">
                    <i class="fa-solid fa-database" style="margin-right: 12px"></i>
                    <span class="menu-title"> Master Data</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('reminder') }}">
                    <i class="fa-solid fa-stopwatch" style="margin-right: 12px"></i>
                    <span class="menu-title"> Reminder Audit</span>
                </a>
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
                <a class="nav-link" data-toggle="collapse" href="#rule-collapse-guest" aria-expanded="false"
                    aria-controls="rule-collapse-guest">
                    <i class="fa-solid fa-file-word" style="margin-right: 14px"></i>
                    <span class="menu-title"> Create/Revision Doc</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="rule-collapse-guest">
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
                <a class="nav-link" data-toggle="collapse" href="#validasi-draft-rule-collapse-admin" aria-expanded="false"
                    aria-controls="validasi-draft-rule-collapse-admin">
                    <i class="fa solid fa-file-circle-check" style="margin-right: 12px"></i>
                    <span class="menu-title"> Document Validation</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="validasi-draft-rule-collapse-admin">
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
            <a class="nav-link" data-toggle="collapse" href="#final-rule-collapse" aria-expanded="false"
                aria-controls="final-rule-collapse">
                <i class="fa-solid fa-file-pdf" style="margin-right: 14px"></i>
                <span class="menu-title"> Final Dokumen</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="final-rule-collapse">
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
            <a class="nav-link" data-toggle="collapse" href="#document-share-collapse" aria-expanded="false"
                aria-controls="document-share-collapse">
                <i class="fa-solid fa-file-import" style="margin-right: 14px"></i>
                <span class="menu-title"> Distributed Document</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="document-share-collapse">
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
