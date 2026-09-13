<?php

namespace App\Providers;

use App\Models\CalendarEvent;
use App\Policies\CalendarEventPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate::policy(CalendarEvent::class, CalendarEventPolicy::class);
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(60)->by('user:' . $request->user()->id)
                : Limit::perMinute(30)->by('ip:' . $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return [
                Limit::perMinute(20)->by('ip:' . $request->ip()),

                Limit::perMinute(5)->by(
                    'login:' . strtolower((string) $request->input('email'))
                        . '|' . $request->ip()
                ),
            ];
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by(
                'register:' . $request->ip()
            );
        });
    }
}
