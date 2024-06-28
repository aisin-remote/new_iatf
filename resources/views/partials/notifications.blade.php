<!-- resources/views/partials/navbar-notifications.blade.php -->

<li class="nav-item dropdown">
    <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
        <i class="fa-solid fa-bell"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
        <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
        @foreach ($notifications as $notification)
            <a class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                    <div class="preview-icon bg-success">
                        <i class="ti-info-alt mx-0"></i>
                    </div>
                </div>
                <div class="preview-item-content">
                    <h6 class="preview-subject font-weight-normal">{{ $notification->data['title'] }}</h6>
                    <p class="font-weight-light small-text mb-0 text-muted">
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>
            </a>
        @endforeach
    </div>
</li>
