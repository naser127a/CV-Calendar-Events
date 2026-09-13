<?php

namespace App\Enums;

enum PermissionPermissionEnum: string
{
    case VIEW = 'view_permissions';
    case CREATE = 'create_permissions';
    case UPDATE = 'edit_permissions';
    case DELETE = 'delete_permissions';
}
