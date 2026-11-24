<?php

namespace App\Http\Controllers;

use App\Models\CustomerNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerNotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $client = auth('client')->user();
        $notifications = $client->customerNotifications()->latest()->limit(30)->get();

        return response()->json([
            'success' => true,
            'unread_count' => $client->customerNotifications()->whereNull('read_at')->count(),
            'notifications' => $notifications->map(fn (CustomerNotification $notification): array => [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'action_url' => $notification->action_url,
                'read_at' => $notification->read_at?->toIso8601String(),
                'created_at' => $notification->created_at?->toIso8601String(),
            ]),
        ]);
    }

    public function markRead(CustomerNotification $notification): JsonResponse
    {
        $this->authorizeClient($notification);
        $notification->update(['read_at' => $notification->read_at ?? now()]);

        return response()->json(['success' => true]);
    }

    public function markAllRead(): JsonResponse
    {
        auth('client')->user()
            ->customerNotifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function destroy(CustomerNotification $notification): JsonResponse
    {
        $this->authorizeClient($notification);
        $notification->delete();

        return response()->json(['success' => true]);
    }

    private function authorizeClient(CustomerNotification $notification): void
    {
        abort_unless($notification->client_id === auth('client')->id(), 404);
    }
}
