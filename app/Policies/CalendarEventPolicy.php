<?php

namespace App\Policies;

use App\Enums\CalendarPermissionEnum;
use App\Models\CalendarEvent;
use App\Models\User;

class CalendarEventPolicy

{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can(
            CalendarPermissionEnum::VIEW->value
        );
    }

    public function view(User $user, CalendarEvent $event): bool
    {
        return $user->can(
            CalendarPermissionEnum::VIEW->value
        ) && $event->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can(
            CalendarPermissionEnum::CREATE->value
        );
    }

    public function update(User $user, CalendarEvent $event): bool
    {
        return $user->can(
            CalendarPermissionEnum::UPDATE->value
        ) && $event->created_by === $user->id;
    }

    public function delete(User $user, CalendarEvent $event): bool
    {
        return $user->can(
            CalendarPermissionEnum::DELETE->value
        ) && $event->created_by === $user->id;
    }

    public function activate(User $user, CalendarEvent $event): bool
    {
        return $user->can(
            CalendarPermissionEnum::ACTIVATE->value
        ) && $event->created_by === $user->id;
    }

    public function deactivate(User $user, CalendarEvent $event): bool
    {
        return $user->can(
            CalendarPermissionEnum::DEACTIVATE->value
        ) && $event->created_by === $user->id;
    }
}
