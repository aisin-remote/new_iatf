<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <!-- Document Control -->
        <li class="nav-item {{ Route::currentRouteName() === 'document_review.dashboard' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('document_review.dashboard') }}">
                <i class="fa-solid fa-file" style="margin-right: 8px"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item {{ Route::currentRouteName() === 'document_review.list' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('document_review.list') }}">
                <i class="fa-solid fa-file" style="margin-right: 8px"></i>
                <span class="menu-title">Document<br>
                    Review Control</span>
            </a>
        </li>
    </ul>
</nav>
