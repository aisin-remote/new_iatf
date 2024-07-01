<li class="nav-item dropdown">
    <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
        <i class="fa-solid fa-bell"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
        <div class="dropdown-header">
            <h4 class="mb-0 font-weight-normal float-left">Notifications</h4>
        </div>
        <div class="dropdown-item">
            <div class="notification-list">
                @forelse ($documents as $document)
                    <div class="notification-item d-flex align-items-center justify-content-between"
                        onclick="markAsRead('{{ $document->id }}')">
                        <div class="notification-content">
                            <p class="notification-title">{{ $document->nomor_dokumen }}</p>
                            <p class="notification-text">{{ $document->nama_dokumen }}</p>
                            <p class="notification-status">{{ $document->status }}</p>
                        </div>
                        <i class="fa-solid fa-info-circle notification-info-icon ml-2"
                            style="width: 36px; height:auto;"></i>
                    </div>
                @empty
                    <p>No notifications found.</p>
                @endforelse
            </div>
        </div>
        {{ $documents->links() }}
    </div>
</li>
