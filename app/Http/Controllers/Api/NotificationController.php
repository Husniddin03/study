<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->notifications()->orderByDesc('created_at');

        if ($request->boolean('unread')) {
            $query->unread();
        }

        $notifications = $query->paginate(30);

        return $this->success([
            'items'        => NotificationResource::collection($notifications),
            'unread_count' => $request->user()->unreadNotificationsCount(),
            'meta'         => ['total' => $notifications->total()],
        ]);
    }

    public function markRead(Request $request, Notification $notification)
    {
        abort_if($notification->user_id !== $request->user()->id, 403);
        $notification->markAsRead();

        return $this->success(new NotificationResource($notification), 'O\'qildi');
    }

    public function markAllRead(Request $request)
    {
        $request->user()->notifications()->unread()
            ->update(['is_read' => true, 'read_at' => now()]);

        return $this->success(null, 'Barchasi o\'qildi deb belgilandi');
    }

    public function destroy(Request $request, Notification $notification)
    {
        abort_if($notification->user_id !== $request->user()->id, 403);
        $notification->delete();

        return $this->success(null, 'Bildirishnoma o\'chirildi');
    }
}
