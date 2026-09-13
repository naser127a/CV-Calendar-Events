<?php

namespace App\Policies;

use App\Enums\ActivityLogPermissionEnum;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class ActivityLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(
            ActivityLogPermissionEnum::VIEW->value
        ) || $user->can(
            ActivityLogPermissionEnum::VIEW_ALL->value
        );
    }

    public function view(
        User $user,
        Activity $activity
    ): bool {
        if (
            $activity->causer_type === User::class
            && $activity->causer_id === $user->id
        ) {
            return $user->can(
                ActivityLogPermissionEnum::VIEW->value
            );
        }

        return $user->can(
            ActivityLogPermissionEnum::VIEW_ALL->value
        );
    }
}
