<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::post('login', [AuthenticationController::class, 'login'])
        ->middleware('throttle:login');
    Route::post('register', [RegistrationController::class, 'register'])
        ->middleware('throttle:register');

    /*
    |--------------------------------------------------------------------------
    | Protected Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        [
            'auth:sanctum',
            'throttle:api'
        ]
    )->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Auth
        |--------------------------------------------------------------------------
        */

        Route::post('logout', [AuthenticationController::class, 'logout']);
        Route::post('logout-all', [AuthenticationController::class, 'logoutAll']);
        Route::get('me', [AuthenticationController::class, 'me']);
        Route::patch('changePassword-password', [PasswordController::class, 'changePassword']);

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */


        Route::prefix('roles')->controller(RoleController::class)->group(function () {
            Route::get('/', 'index')->name('roles.index');
            Route::post('/', 'store')->name('roles.store');

            // المسارات الإضافية
            Route::get('{role}/permissions', 'permissions')->name('roles.permissions');
            Route::post('{role}/permissions', 'syncPermissions')->name('roles.sync-permissions');

            Route::get('{role}', 'show')->name('roles.show');
            Route::put('{role}', 'update')->name('roles.update');
            Route::delete('{role}', 'destroy')->name('roles.destroy');
        });        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        Route::apiResource('permissions', PermissionController::class);

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        Route::apiResource('users', UserController::class);

        Route::prefix('users')->controller(UserController::class)->group(function () {

            Route::patch('{user}/activate', 'activate')
                ->name('users.activate');

            Route::patch('{user}/deactivate', 'deactivate')
                ->name('users.deactivate');

            Route::patch('{user}/change-role', 'changeRole')
                ->name('users.change-role');
            Route::get('/trashed', 'trashed');
            Route::delete('/{id}/soft-delete', 'softDelete');
            Route::post('/{id}/restore', 'restore');
            Route::delete('/{id}/force-delete', 'forceDelete');
        });

        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */

        Route::prefix('profile')
            ->controller(ProfileController::class)
            ->group(function () {
                Route::get('/', 'me');
                Route::patch('/', 'update');
            });

        /*
        |--------------------------------------------------------------------------
        | Calendar Events
        |--------------------------------------------------------------------------
        */

        Route::apiResource('events', CalendarController::class);

        Route::prefix('events')->controller(CalendarController::class)->group(function () {

            Route::patch('{calendarEvent}/activate', 'activate')
                ->name('events.activate');

            Route::patch('{calendarEvent}/deactivate', 'deactivate')
                ->name('events.deactivate');
        });

        /*
        |--------------------------------------------------------------------------
        | News
        |--------------------------------------------------------------------------
        */


        Route::prefix('news')->controller(NewsController::class)->group(function () {

            Route::patch('{news}/toggle-status', 'toggleStatus')
                ->name('news.toggle-status');

            Route::patch('{news}/toggle-breaking', 'toggleBreaking')
                ->name('news.toggle-breaking');
        });
        Route::apiResource('news', NewsController::class);

        /*
        |--------------------------------------------------------------------------
        | Logs
        |--------------------------------------------------------------------------
        */
        Route::get('/activity-logs', [ActivityLogController::class, 'index']);


        /*
        |--------------------------------------------------------------------------
        | Notifications
        |--------------------------------------------------------------------------
        */
        Route::get(
            '/notifications',
            [NotificationController::class, 'index']
        );

        Route::get(
            '/notifications/unread',
            [NotificationController::class, 'unread']
        );

        Route::patch(
            '/notifications/read-all',
            [NotificationController::class, 'markAllAsRead']
        );

        Route::patch(
            '/notifications/{id}/read',
            [NotificationController::class, 'markAsRead']
        );

        Route::delete(
            '/notifications/{id}',
            [NotificationController::class, 'destroy']
        );
        Route::get(
            '/notifications/unread-count',
            [NotificationController::class, 'unreadCount']
        );
    });
});
