<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Auth::user()->userNotifications()->latest()->paginate(15);

        return view('user.notifications', compact('notifications'));
    }

    public function markAllRead(): RedirectResponse
    {
        Auth::user()->userNotifications()->where('is_read', false)->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }
}