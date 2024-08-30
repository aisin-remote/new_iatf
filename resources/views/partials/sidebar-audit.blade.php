<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard.audit') }}">
                <i class="fa-solid fa-house" style="margin-right: 8px"></i>
                <span class="menu-title"> Dashboard</span>
            </a>
        </li>
        @role('admin')
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#masterdata" aria-expanded="false"
                    aria-controls="masterdata">
                    <i class="fa-solid fa-database" style="margin-right: 12px"></i>
                    <span class="menu-title"> Master Data</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="masterdata">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('masterdata.audit') }}">Audit</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('masterdata.documentAudit') }}">Document Audit</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="">Audit Control</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endrole
        <li class="nav-item">
            <a class="nav-link" href="{{ route('auditControl') }}">
                <i class="fa-solid fa-list-check" style="margin-right: 8px"></i>
                <span class="menu-title"> Audit Control</span>
            </a>
        </li>
    </ul>
</nav>
