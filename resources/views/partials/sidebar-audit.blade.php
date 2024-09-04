<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <!-- Dashboard Audit -->
        <li class="nav-item {{ request()->routeIs('dashboard.audit') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard.audit') }}">
                <i class="fa-solid fa-house" style="margin-right: 8px"></i>
                <span class="menu-title"> Dashboard</span>
            </a>
        </li>

        <!-- Master Data Menu for Admin -->
        @role('admin')
            <li class="nav-item {{ request()->routeIs('masterdata.*') ? 'active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#masterdata"
                    aria-expanded="{{ request()->routeIs('masterdata.*') ? 'true' : 'false' }}" aria-controls="masterdata">
                    <i class="fa-solid fa-database" style="margin-right: 12px"></i>
                    <span class="menu-title"> Master Data</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('masterdata.*') ? 'show' : '' }}" id="masterdata">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('masterdata.audit') ? 'active' : '' }}"
                                href="{{ route('masterdata.audit') }}">Audit</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('masterdata.documentAudit') ? 'active' : '' }}"
                                href="{{ route('masterdata.itemAudit') }}">Item Audit</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('masterdata.auditControl') ? 'active' : '' }}"
                                href="{{ route('masterdata.auditControl') }}">Audit Control</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endrole

        <!-- Audit Control -->
        <li class="nav-item {{ request()->routeIs('index.auditControl') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('index.auditControl') }}">
                <i class="fa-solid fa-list-check" style="margin-right: 8px"></i>
                <span class="menu-title"> Audit Control</span>
            </a>
        </li>
    </ul>
</nav>
