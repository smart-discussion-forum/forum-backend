<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Full-page view of the authenticated user's notifications.
     */
    public function page()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * List the authenticated user's notifications.
     * Pass ?unread_only=1 to only get unread ones.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $notifications = $request->boolean('unread_only')
            ? $user->unreadNotifications()->latest()->get()
            : $user->notifications()->latest()->get();

        return response()->json($notifications);
    }

    public function markRead(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
