<?php

namespace App\Policies;

use App\Enums\PermissionPermissionEnum;
use App\Models\Permission;
use App\Models\User;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(
            PermissionPermissionEnum::VIEW->value
        );
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->can(
            PermissionPermissionEnum::VIEW->value
        );
    }

    public function create(User $user): bool
    {
        return $user->can(
            PermissionPermissionEnum::CREATE->value
        );
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->can(
            PermissionPermissionEnum::UPDATE->value
        );
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $user->can(
            PermissionPermissionEnum::DELETE->value
        );
    }
}
