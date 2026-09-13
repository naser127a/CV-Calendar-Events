<?php

namespace App\Listeners;

use App\Events\Role\RoleChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class ClearRoleCache
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */

    public function handle(RoleChanged $event): void
    {
        Cache::forget('roles.all');
    }
}
