<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <!-- Document Control -->
        <li class="nav-item {{ Route::currentRouteName() === 'document_control.list' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('document_control.list') }}">
                <i class="fa-solid fa-file" style="margin-right: 8px"></i>
                <span class="menu-title">Document<br>
                    Obsolete Control</span>
            </a>
        </li>
        <li class="nav-item {{ Route::currentRouteName() === 'document_control_review.list' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('document_control_review.list') }}">
                <i class="fa-solid fa-file" style="margin-right: 8px"></i>
                <span class="menu-title">Document<br>
                    Review Control</span>
            </a>
        </li>
    </ul>
</nav>
