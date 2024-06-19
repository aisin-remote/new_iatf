@foreach ($notifications as $notification)
    <a class="dropdown-item">
        {{ $notification->command }}
    </a>
@endforeach
