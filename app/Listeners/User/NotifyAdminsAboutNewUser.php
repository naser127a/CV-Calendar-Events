<?php

namespace App\Listeners\User;

use App\Events\User\UserRegistered;

use App\Models\User;
use App\Notifications\NewUserRegisteredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAdminsAboutNewUser implements ShouldQueue
{
    public function handle(UserRegistered $event): void
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        foreach ($admins as $admin) {
            $admin->notify(
                new NewUserRegisteredNotification($event->user)
            );
        }
    }
}
