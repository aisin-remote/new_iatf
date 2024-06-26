<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $notification = auth()->user()->unreadNotifications->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return redirect()->back();
    }
    public function showNotifications()
    {
        $notifications = auth()->user()->notifications;
        return view('partials.notifications', compact('notifications'));
    }
}
