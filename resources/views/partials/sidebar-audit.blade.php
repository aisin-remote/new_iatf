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
                <span class="menu-title">List Audit Control</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ Route::currentRouteName() === 'index.auditControl' ? 'show' : '' }}"
                id="auditControlMenu">
                <ul class="nav flex-column sub-menu">
                    @if (auth()->user()->hasRole('admin'))
                        <!-- Jika role adalah admin, tampilkan semua departemen -->
                        @php
                            $allDepartments = App\Models\Departemen::all(); // Mengambil semua departemen dari database
                        @endphp
                        @foreach ($allDepartments as $departemen)
                            @if ($departemen->nama_departemen !== 'Aisin Indonesia')
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::currentRouteName() === 'index.auditControl' && request()->route('id') == $departemen->id ? 'active' : '' }}"
                                        href="{{ route('index.auditControl', $departemen->id) }}">
                                        <i class="fa-solid fa-folder" style="margin-right: 8px"></i>
                                        <span class="menu-title">{{ $departemen->aliases }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @elseif (auth()->user()->hasRole('guest'))
                        <!-- Jika role adalah guest, tampilkan departemen pengguna saat ini saja -->
                        @if (auth()->user()->departemen)
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() === 'index.auditControl' && request()->route('id') == auth()->user()->departemen->id ? 'active' : '' }}"
                                    href="{{ route('index.auditControl', auth()->user()->departemen->id) }}">
                                    <i class="fa-solid fa-folder" style="margin-right: 8px"></i>
                                    <span class="menu-title">{{ auth()->user()->departemen->aliases }}</span>
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <span class="nav-link">No departments available for this user.</span>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
        </li>
    </ul>
</nav>
