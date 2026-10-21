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

    private function ownsEvent(User $user, CalendarEvent $event): bool
    {
        return (int) $event->created_by === (int) $user->id;
    }

    private function canManageEvent(
        User $user,
        CalendarEvent $event,
        CalendarPermissionEnum $permission
    ): bool {
        return $user->can($permission->value)
            && (
                $this->ownsEvent($user, $event)
                || $user->can(CalendarPermissionEnum::MANAGE_ALL->value)
            );
    }

    public function viewAny(User $user): bool
    {
        return $user->can(CalendarPermissionEnum::VIEW->value)
            || $user->can(CalendarPermissionEnum::VIEW_ALL->value);
    }

    public function view(User $user, CalendarEvent $event): bool
    {
        return (
            $user->can(CalendarPermissionEnum::VIEW->value)
            && $this->ownsEvent($user, $event)
        ) || $user->can(CalendarPermissionEnum::VIEW_ALL->value);
    }

    public function create(User $user): bool
    {
        return $user->can(
            CalendarPermissionEnum::CREATE->value
        );
    }

    public function update(User $user, CalendarEvent $event): bool
    {
        return $this->canManageEvent($user, $event, CalendarPermissionEnum::UPDATE);
    }

    public function delete(User $user, CalendarEvent $event): bool
    {
        return $this->canManageEvent($user, $event, CalendarPermissionEnum::DELETE);
    }

    public function activate(User $user, CalendarEvent $event): bool
    {
        return $this->canManageEvent($user, $event, CalendarPermissionEnum::ACTIVATE);
    }

    public function deactivate(User $user, CalendarEvent $event): bool
    {
        return $this->canManageEvent($user, $event, CalendarPermissionEnum::DEACTIVATE);
    }
}
