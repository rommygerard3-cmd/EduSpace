<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->appNotifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, AppNotification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        if (! $notification->read_at) {
            $notification->forceFill(['read_at' => now()])->save();
        }

        return back()->with('success', 'Notification marquée comme lue.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->appNotifications()->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('success', 'Toutes les notifications sont marquées comme lues.');
    }
}
