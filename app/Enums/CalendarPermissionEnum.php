<?php

namespace App\Enums;

enum CalendarPermissionEnum: string
{
    case VIEW = 'view_calendar';
    case VIEW_ALL = 'view_all_calendar_events';
    case CREATE = 'create_calendar';
    case UPDATE = 'edit_calendar';
    case MANAGE_ALL = 'manage_all_calendar_events';
    case DELETE = 'delete_calendar';
    case ACTIVATE = 'activate_calendar';
    case DEACTIVATE = 'deactivate_calendar';
}
