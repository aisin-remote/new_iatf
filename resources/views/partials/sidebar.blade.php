<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('home') }}">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <i class="icon-layout menu-icon"></i>
                <span class="menu-title">Document</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic" style="">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{ route('upload-dokumen') }}">Upload</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('validate-dokumen') }}">Validate</a></li>
                </ul>
            </div>
        </li>        

        <li class="nav-item">
            <a class="nav-link btn btn-link" href="{{ route('user') }}">
                <i class="icon-head menu-icon"></i>
                <span class="menu-title">User</span> 
            </a>
        </li>
    </ul>
</nav>
