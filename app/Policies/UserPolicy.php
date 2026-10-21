<?php

namespace App\Policies;

use App\Enums\UserPermissionEnum;
use App\Models\Role;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(
            UserPermissionEnum::VIEW->value
        );
    }

    public function view(User $user, User $target): bool
    {
        return $user->can(
            UserPermissionEnum::VIEW->value
        );
    }

    public function create(User $user): bool
    {
        return $user->can(
            UserPermissionEnum::CREATE->value
        );
    }

    public function assignRole(User $user, Role $role): bool
    {
        if (
            in_array($role->name, ['admin', 'super_admin'], true)
            && ! $user->can(UserPermissionEnum::ASSIGN_PRIVILEGED_ROLES->value)
        ) {
            return false;
        }

        return $role->permissions->every(
            fn ($permission): bool => $user->checkPermissionTo($permission)
        );
    }

    public function update(User $user, User $target): bool
    {
        return $user->can(
            UserPermissionEnum::UPDATE->value
        );
    }

    public function delete(User $user, User $target): bool
    {
        return $user->can(
            UserPermissionEnum::DELETE->value
        );
    }

    public function activate(User $user, User $target): bool
    {
        return $user->can(
            UserPermissionEnum::ACTIVATE->value
        );
    }

    public function deactivate(User $user, User $target): bool
    {
        return $user->can(
            UserPermissionEnum::DEACTIVATE->value
        );
    }

    public function viewTrashed(User $user): bool
    {
        return $user->can(UserPermissionEnum::VIEW->value);
    }

    public function restore(User $user, User $target): bool
    {
        return $user->can(UserPermissionEnum::UPDATE->value);
    }

    public function forceDelete(User $user, User $target): bool
    {
        return $user->can(UserPermissionEnum::DELETE->value);
    }
}
