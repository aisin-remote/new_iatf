<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <!-- Document Control -->
        <li class="nav-item {{ Route::currentRouteName() === 'document_control.list' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('document_control.list') }}">
                <i class="fa-solid fa-file" style="margin-right: 8px"></i>
                <span class="menu-title">Document Control</span>
            </a>
        </li>
    </ul>
</nav>
