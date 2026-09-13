<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;

class NotificationService
{
    public function __construct(
        private NotificationRepositoryInterface $notifications
    ) {}

    public function paginate(
        User $user,
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->notifications->paginateForUser(
            $user,
            $perPage
        );
    }

    public function unread(
        User $user,
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->notifications->unreadForUser(
            $user,
            $perPage
        );
    }

    public function send(
        User $user,
        Notification $notification
    ): void {
        $user->notify($notification);
    }

    public function markAsRead(
        User $user,
        string $id
    ): bool {
        $notification = $this->notifications->findForUser(
            $user,
            $id
        );

        if (! $notification) {
            return false;
        }

        $this->notifications->markAsRead($notification);

        return true;
    }

    public function markAllAsRead(User $user): void
    {
        $this->notifications->markAllAsRead($user);
    }

    public function delete(
        User $user,
        string $id
    ): bool {
        $notification = $this->notifications->findForUser(
            $user,
            $id
        );

        if (! $notification) {
            return false;
        }

        return $this->notifications->delete($notification);
    }
}
