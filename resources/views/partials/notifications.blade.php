<li class="nav-item dropdown">
    <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
        <i class="fa-solid fa-bell"></i>
        @if ($notificationCount > 0)
            <span class="notification-dot"></span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown"
        style="width: 400px;">
        <style>
            .notification-list {
                max-height: 400px;
                overflow-y: auto;
            }

            .notification-comment,
            .notification-text {
                word-wrap: break-word;
                white-space: normal;
            }

            .notification-dot {
                position: absolute;
                top: 10px;
                right: 10px;
                height: 10px;
                width: 10px;
                background-color: red;
                border-radius: 50%;
                display: inline-block;
            }
        </style>
        <div class="dropdown-header">
            <h4 class="mb-0 font-weight-normal float-left">Notifications</h4>
            <button type="button" class="close" aria-label="Close" data-toggle="dropdown">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="dropdown-item">
            <div class="notification-list">
                @forelse ($documents as $document)
                    <div class="notification-item d-flex align-items-start justify-content-between mb-3 p-3 border" id="notification-{{ $document->id }}"
                        style="cursor: pointer;">
                        <div class="notification-content flex-grow-1">
                            <p class="notification-title mb-1">{{ $document->nomor_dokumen }}</p>
                            <p class="notification-text mb-1">{{ $document->nama_dokumen }}</p>
                            <p class="notification-status mb-1">{{ $document->status }}</p>
                            <p class="notification-comment mb-0">
                                @if (isset($document->is_shared) && $document->is_shared)
                                    dokumen baru masuk
                                @else
                                    {{ $document->comment }}
                                @endif
                            </p>
                        </div>
                        <i class="fa-solid fa-info-circle notification-info-icon ml-2"
                            style="width: 36px; height:auto;"></i>
                    </div>
                @empty
                    <p>No notifications found.</p>
                @endforelse
            </div>
        </div>
    </div>
</li>
<script>
    function markNotificationAsRead(notificationId) {
        $.ajax({
            url: '/notification/read/' + notificationId,
            method: 'GET',
            success: function() {
                $('#notification-' + notificationId).remove(); // Menghapus notifikasi dari daftar
            }
        });
    }
</script>
