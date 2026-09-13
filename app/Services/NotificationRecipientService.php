<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationRecipientService
{
    public function usersWithPermission(
        string $permission
    ): Collection {
        return User::query()
            ->where('status', true)
            ->whereHas('role', function ($query) use ($permission) {
                $query->whereHas('permissions', function ($query) use ($permission) {
                    $query->where('name', $permission);
                });
            })
            ->get();
    }
}
