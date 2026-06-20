<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /** Cap how many we ever ship to the dropdown. */
    private const LIMIT = 50;

    /**
     * GET /notifications — latest notifications (mapped to the dropdown shape)
     * plus the current unread count.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $items = $user->notifications()
            ->latest()
            ->limit(self::LIMIT)
            ->get()
            ->map(fn (DatabaseNotification $n) => $this->present($n));

        return response()->json([
            'notifications' => $items,
            'unread_count'  => $user->unreadNotifications()->count(),
        ]);
    }

    /** GET /notifications/unread-count — cheap poll for the badge. */
    public function unreadCount(Request $request)
    {
        return response()->json([
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    /** POST /notifications/read-all — clear the badge. */
    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['ok' => true]);
    }

    /** PATCH /notifications/{id}/read — mark a single notification read. */
    public function read(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['ok' => true]);
    }

    private function present(DatabaseNotification $n): array
    {
        $data = $n->data;

        return [
            'id'          => $n->id,
            'category'    => $data['category'] ?? 'account',
            'title'       => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'icon'        => $data['icon'] ?? 'bi-bell',
            'read'        => $n->read_at !== null,
            'timestamp'   => $n->created_at->toIso8601String(),
        ];
    }
}
