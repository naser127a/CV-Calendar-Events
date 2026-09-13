<?php

namespace App\Policies;

use App\Enums\RolePermissionEnum;
use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(
            RolePermissionEnum::VIEW->value
        );
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can(
            RolePermissionEnum::VIEW->value
        );
    }

    public function create(User $user): bool
    {
        return $user->can(
            RolePermissionEnum::CREATE->value
        );
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can(
            RolePermissionEnum::UPDATE->value
        );
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->can(
            RolePermissionEnum::DELETE->value
        );
    }
}
