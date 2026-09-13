<?php

namespace App\Enums;

enum NewsPermissionEnum: string
{
    case VIEW = 'view_news';
    case CREATE = 'create_news';
    case UPDATE = 'edit_news';
    case DELETE = 'delete_news';
    case ACTIVATE = 'activate_news';
    case DEACTIVATE = 'deactivate_news';
    case VIEW_ALL = 'view_all_news';
}
