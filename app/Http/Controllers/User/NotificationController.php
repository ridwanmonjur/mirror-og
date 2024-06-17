<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notifications;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = Notifications::where('id', $id)
            ->where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $user->id)
            ->firstOrFail();

        if (! $notification) {
            return response()->json(['success' => false, 'error' => 'Notification not found or does not belong to the user'], 404);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Notification marked as read'], 200);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        Notifications::where('notifiable_id', $user->id)
            ->where('notifiable_type', User::class)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);

        return response()->json(['success' => true, 'message' => 'All user notifications marked as read'], 200);
    }
}
