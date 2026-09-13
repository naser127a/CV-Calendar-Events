<?php

namespace App\Listeners\News;

use App\Events\NewsCreated;
use App\Notifications\NewsCreatedNotification;
use App\Models\User;

class SendNewsCreatedNotification
{
    public function handle(NewsCreated $event): void
    {
        User::query()
            ->where('id', '!=', $event->userId)
            ->each(function (User $user) use ($event) {
                $user->notify(
                    new NewsCreatedNotification($event->news)
                );
            });
    }
}
