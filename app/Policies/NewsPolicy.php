<?php

namespace App\Policies;

use App\Enums\NewsPermissionEnum;
use App\Models\News;
use App\Models\User;

class NewsPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(
            NewsPermissionEnum::VIEW_ALL->value
        );
    }

    public function view(User $user, News $news): bool
    {
        return $user->can(
            NewsPermissionEnum::VIEW->value
        );
    }

    public function create(User $user): bool
    {
        return $user->can(
            NewsPermissionEnum::CREATE->value
        );
    }

    public function update(User $user, News $news): bool
    {
        return $user->can(
            NewsPermissionEnum::UPDATE->value
        );
    }

    public function delete(User $user, News $news): bool
    {
        return $user->can(
            NewsPermissionEnum::DELETE->value
        );
    }

    public function activate(User $user, News $news): bool
    {
        return $user->can(
            NewsPermissionEnum::ACTIVATE->value
        );
    }

    public function deactivate(User $user, News $news): bool
    {
        return $user->can(
            NewsPermissionEnum::DEACTIVATE->value
        );
    }
}
