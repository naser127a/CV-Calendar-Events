<?php

namespace App\Listeners\User;

use App\Events\User\UserRoleChanged;
use App\Notifications\UserRoleChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Throwable;

class NotifyUserRoleChanged implements ShouldQueue
{
    /** Number of times to attempt the job */
    public int $tries = 3;

    /** Backoff seconds (or array of backoff values) */
    public int|array $backoff = 10;

    public function handle(UserRoleChanged $event): void
    {
        $event->user->notify(
            new UserRoleChangedNotification(
                oldRoles: $event->oldRoles,
                newRoles: $event->newRoles,
            )
        );
    }


    public function failed(
        UserRoleChanged $event,
        Throwable $exception
    ): void {
        logger()->error(
            'Failed to send role change notification.',
            [
                'user_id' => $event->user->id,
                'exception' => $exception->getMessage(),
            ]
        );
    }
}
