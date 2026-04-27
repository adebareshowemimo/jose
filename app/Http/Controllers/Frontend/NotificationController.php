<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->paginate(20);

        return view('pages.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $user->unreadNotifications()->count(),
            'dashboardType' => $this->dashboardType($user),
            'routeNames' => $this->routeNames($user),
        ]);
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    public function markRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        return $url ? redirect($url) : back();
    }

    private function dashboardType($user): string
    {
        return match ($user->role?->name) {
            'employer' => 'employer',
            'candidate' => 'candidate',
            'admin' => 'admin',
            default => 'candidate',
        };
    }

    private function routeNames($user): array
    {
        $prefix = match ($user->role?->name) {
            'employer' => 'employer',
            default => 'user',
        };

        return [
            'index' => "{$prefix}.notifications",
            'readAll' => "{$prefix}.notifications.read-all",
            'read' => "{$prefix}.notifications.read",
        ];
    }
}
