<?php

namespace App\Enums;

enum UserPermissionEnum: string
{
    case VIEW = 'view_users';
    case CREATE = 'create_users';
    case UPDATE = 'edit_users';
    case DELETE = 'delete_users';
    case ACTIVATE = 'activate_users';
    case DEACTIVATE = 'deactivate_users';
}
