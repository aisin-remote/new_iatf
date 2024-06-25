<!-- resources/views/partials/notifications.blade.php -->
@if(auth()->check() && auth()->user()->unreadNotifications->count())
    <div class="alert alert-info">
        <h4 class="alert-heading">Notifications</h4>
        <ul class="list-group">
            @foreach(auth()->user()->unreadNotifications as $notification)
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $notification->data['title'] }}</strong>
                            <p>{{ $notification->data['message'] }}</p>
                            <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-link">View Details</a>
                        </div>
                        <form method="POST" action="{{ route('markAsRead', $notification->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-primary">Mark as Read</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endif
