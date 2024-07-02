<li class="nav-item dropdown">
    <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
        <i class="fa-solid fa-bell"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown"
        style="width: 400px; height: auto;">
        <div class="dropdown-header">
            <h4 class="mb-0 font-weight-normal float-left">Notifications</h4>
        </div>
        <div class="dropdown-item">
            <div class="notification-list">
                @forelse ($documents as $document)
                    <div class="notification-item d-flex align-items-start justify-content-between mb-3 p-3 border"
                        onclick="markAsRead('{{ $document->id }}')" style="cursor: pointer;">
                        <div class="notification-content flex-grow-1">
                            <p class="notification-title mb-1">{{ $document->nomor_dokumen }}</p>
                            <p class="notification-text mb-1">{{ $document->nama_dokumen }}</p>
                            <p class="notification-status mb-1">{{ $document->status }}</p>
                            <p class="notification-comment mb-0">{{ $document->comment }}</p>
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
