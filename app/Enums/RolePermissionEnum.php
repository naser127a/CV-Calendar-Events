<?php

namespace App\Enums;

enum RolePermissionEnum: string
{
    case VIEW = 'view_roles';
    case CREATE = 'create_roles';
    case UPDATE = 'edit_roles';
    case DELETE = 'delete_roles';
    case ACTIVATE = 'activate_roles';
    case DEACTIVATE = 'deactivate_roles';
    case ASSIGN_PERMISSIONS = 'assign_role_permissions';
}
