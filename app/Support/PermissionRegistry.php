<?php

namespace App\Support;

use App\Enums\ActivityLogPermissionEnum;
use App\Enums\CalendarPermissionEnum;
use App\Enums\NewsPermissionEnum;
use App\Enums\PermissionPermissionEnum;
use App\Enums\RolePermissionEnum;
use App\Enums\UserPermissionEnum;

class PermissionRegistry
{
    public static function enums(): array
    {
        return [

            UserPermissionEnum::class,

            RolePermissionEnum::class,

            PermissionPermissionEnum::class,

            CalendarPermissionEnum::class,

            NewsPermissionEnum::class,

            ActivityLogPermissionEnum::class

        ];
    }
}
