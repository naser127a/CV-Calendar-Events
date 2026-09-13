<?php

namespace App\Enums;

enum CalendarPermissionEnum: string
{
    case VIEW = 'view_calendar';
    case CREATE = 'create_calendar';
    case UPDATE = 'edit_calendar';
    case DELETE = 'delete_calendar';
    case ACTIVATE = 'activate_calendar';
    case DEACTIVATE = 'deactivate_calendar';
}
