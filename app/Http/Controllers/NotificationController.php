<?php

namespace App\Http\Controllers;

use App\Models\IndukDokumen;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $documents = IndukDokumen::where('user_id', $user->id)
            ->paginate(10); // Mengambil dokumen berdasarkan status dan user
        return view('partials.notifications', compact('documents'));
    }
}
