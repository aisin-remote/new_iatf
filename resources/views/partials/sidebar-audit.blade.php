<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <!-- Dashboard Audit -->
        <li class="nav-item {{ Route::currentRouteName() === 'dashboard.audit' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard.audit') }}">
                <i class="fa-solid fa-house" style="margin-right: 8px"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <!-- Master Data Menu for Admin -->
        @role('admin')
            <li
                class="nav-item {{ in_array(Route::currentRouteName(), ['masterdata.audit', 'masterdata.itemAudit', 'masterdata.auditControl']) ? 'active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#masterdata"
                    aria-expanded="{{ in_array(Route::currentRouteName(), ['masterdata.audit', 'masterdata.itemAudit', 'masterdata.auditControl']) ? 'true' : 'false' }}"
                    aria-controls="masterdata">
                    <i class="fa-solid fa-database" style="margin-right: 12px"></i>
                    <span class="menu-title">Master Data</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ in_array(Route::currentRouteName(), ['masterdata.audit', 'masterdata.itemAudit', 'masterdata.auditControl']) ? 'show' : '' }}"
                    id="masterdata">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() === 'masterdata.audit' ? 'active' : '' }}"
                                href="{{ route('masterdata.audit') }}">Audit</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() === 'masterdata.itemAudit' ? 'active' : '' }}"
                                href="{{ route('masterdata.itemAudit') }}">Item Audit</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() === 'masterdata.auditControl' ? 'active' : '' }}"
                                href="{{ route('masterdata.auditControl') }}">Audit Control</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endrole

        <!-- Audit Control -->
        <li class="nav-item {{ Route::currentRouteName() === 'index.auditControl' ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#auditControlMenu"
                aria-expanded="{{ Route::currentRouteName() === 'index.auditControl' ? 'true' : 'false' }}"
                aria-controls="auditControlMenu">
                <i class="fa-solid fa-list-check" style="margin-right: 8px"></i>
                <span class="menu-title">Audit Control</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ Route::currentRouteName() === 'index.auditControl' ? 'show' : '' }}"
                id="auditControlMenu">
                <ul class="nav flex-column sub-menu">
                    @foreach ($departemens as $departemen)
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() === 'index.auditControl' && request()->route('id') == $departemen->id ? 'active' : '' }}"
                                href="{{ route('index.auditControl', $departemen->id) }}">
                                <i class="fa-solid fa-folder" style="margin-right: 8px"></i>
                                <span class="menu-title">{{ $departemen->nama_departemen }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </li>

        <!-- Document Control -->
        <li class="nav-item {{ Route::currentRouteName() === 'document_control.list' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('document_control.list') }}">
                <i class="fa-solid fa-file" style="margin-right: 8px"></i>
                <span class="menu-title">Document Control</span>
            </a>
        </li>
    </ul>
</nav>
