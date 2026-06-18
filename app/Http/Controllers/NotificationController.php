<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = AppNotification::forUser($user);

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            }
        }

        $notifications = $query->latest()->paginate(20);

        $unreadCount = AppNotification::forUser($user)->unread()->count();

        $counts = [
            'all' => AppNotification::forUser($user)->count(),
            'assignment' => AppNotification::forUser($user)->byType('assignment')->count(),
            'submission' => AppNotification::forUser($user)->byType('submission')->count(),
            'grade' => AppNotification::forUser($user)->byType('grade')->count(),
            'message' => AppNotification::forUser($user)->byType('message')->count(),
            'announcement' => AppNotification::forUser($user)->byType('announcement')->count(),
            'system' => AppNotification::forUser($user)->byType('system')->count(),
            'unread' => $unreadCount,
        ];

        return view('notifications.index', compact('notifications', 'counts', 'unreadCount'));
    }

    public function show(AppNotification $notification)
    {
        $user = Auth::user();

        if ($notification->notifiable_type !== get_class($user) || $notification->notifiable_id !== $user->id) {
            abort(403);
        }

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        $data = $notification->data;

        if (!empty($data['action_url'])) {
            return redirect($data['action_url']);
        }

        return view('notifications.show', compact('notification'));
    }

    public function markAllRead()
    {
        $user = Auth::user();

        AppNotification::forUser($user)->unread()->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function unreadCount()
    {
        $user = Auth::user();

        $count = AppNotification::forUser($user)->unread()->count();

        if (request()->wantsJson()) {
            return response()->json(['count' => $count]);
        }

        return $count;
    }
}
