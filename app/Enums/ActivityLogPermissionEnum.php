<?php

namespace App\Enums;

enum ActivityLogPermissionEnum: string
{
    case VIEW = 'view_activity_log';
    case VIEW_ALL = 'view_all_activity_logs';
}
