<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notifications
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();

        $notifications = $this->notifications->paginate(
            $user,
            (int) $request->input('per_page', 15)
        );

        return ApiResponse::paginated(
            NotificationResource::collection($notifications),
            'Notifications retrieved successfully.'
        );
    }

    public function unread(Request $request)
    {
        $user = Auth::user();

        $notifications = $this->notifications->unread(
            $user,
            (int) $request->input('per_page', 15)
        );

        return ApiResponse::paginated(
            NotificationResource::collection($notifications),
            'Unread notifications retrieved successfully.'
        );
    }

    public function markAsRead(string $id)
    {
        $user = Auth::user();

        $marked = $this->notifications->markAsRead(
            $user,
            $id
        );

        if (! $marked) {
            return ApiResponse::error(
                'Notification not found.',
                [],
                404
            );
        }

        return ApiResponse::success(
            null,
            'Notification marked as read.'
        );
    }

    public function markAllAsRead()
    {
        $this->notifications->markAllAsRead(
            Auth::user()
        );

        return ApiResponse::success(
            null,
            'All notifications marked as read.'
        );
    }

    public function destroy(string $id)
    {
        $deleted = $this->notifications->delete(
            Auth::user(),
            $id
        );

        if (! $deleted) {
            return ApiResponse::error(
                'Notification not found.',
                [],
                404
            );
        }

        return ApiResponse::success(
            null,
            'Notification deleted successfully.'
        );
    }

    public function unreadCount()
    {
        /*$user @var User*/
        $user = Auth::user();

        return ApiResponse::success([
            'count' => $user ? $user->unreadNotifications()->count() : 0,
        ]);
    }
}
