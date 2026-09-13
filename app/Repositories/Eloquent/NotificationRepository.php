<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function paginateForUser(
        User $user,
        int $perPage = 15
    ): LengthAwarePaginator {
        return $user
            ->notifications()
            ->latest()
            ->paginate($perPage);
    }

    public function unreadForUser(
        User $user,
        int $perPage = 15
    ): LengthAwarePaginator {
        return $user
            ->unreadNotifications()
            ->latest()
            ->paginate($perPage);
    }

    public function findForUser(
        User $user,
        string $id
    ): ?DatabaseNotification {
        return $user
            ->notifications()
            ->where('id', $id)
            ->first();
    }

    public function markAsRead(
        DatabaseNotification $notification
    ): void {
        $notification->markAsRead();
    }

    public function markAllAsRead(User $user): void
    {
        $user
            ->unreadNotifications()
            ->update([
                'read_at' => now(),
            ]);
    }

    public function delete(
        DatabaseNotification $notification
    ): bool {
        return (bool) $notification->delete();
    }
}
