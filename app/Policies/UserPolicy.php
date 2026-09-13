<?php

namespace App\Policies;

use App\Enums\UserPermissionEnum;
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
}
