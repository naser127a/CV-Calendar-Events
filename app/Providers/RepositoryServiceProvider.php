<?php

namespace App\Providers;

use App\Repositories\Contracts\CalendarEventRepositoryInterface;
use App\Repositories\Contracts\NewsRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\CalendarEventRepository;
use App\Repositories\Eloquent\NewsRepository;
use App\Repositories\Eloquent\NotificationRepository;
use App\Repositories\Eloquent\UserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            CalendarEventRepositoryInterface::class,
            CalendarEventRepository::class
        );
        $this->app->bind(
            NewsRepositoryInterface::class,
            NewsRepository::class
        );

        $this->app->bind(
            NotificationRepositoryInterface::class,
            NotificationRepository::class
        );
    }

    public function boot(): void {}
}
