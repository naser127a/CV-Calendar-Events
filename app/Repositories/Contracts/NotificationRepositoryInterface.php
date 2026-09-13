<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;

interface NotificationRepositoryInterface
{
    public function paginateForUser(
        User $user,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function unreadForUser(
        User $user,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function findForUser(
        User $user,
        string $id
    ): ?DatabaseNotification;

    public function markAsRead(
        DatabaseNotification $notification
    ): void;

    public function markAllAsRead(User $user): void;

    public function delete(
        DatabaseNotification $notification
    ): bool;
}
