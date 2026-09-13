<?php

namespace App\Providers;

use App\Events\NewsCreated;

use App\Events\User\UserRegistered;
use App\Events\User\UserRoleChanged;

use App\Listeners\News\SendNewsCreatedNotification;

use App\Listeners\User\NotifyAdminsAboutNewUser;
use App\Listeners\User\NotifyUserRoleChanged;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NewsCreated::class => [
            SendNewsCreatedNotification::class,
        ],
        UserRegistered::class => [

            NotifyAdminsAboutNewUser::class
        ],

        UserRoleChanged::class =>
        [
            NotifyUserRoleChanged::class
        ]

    ];
}
