<?php

namespace App\Enums;

enum ActivityActionEnum: string
{
    // Authentication
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case LOGIN_FAILED = 'login_failed';
    case PASSWORD_CHANGED = 'password_changed';

    // Profile
    case PROFILE_UPDATED = 'profile_updated';
    case AVATAR_UPDATED = 'avatar_updated';

    // Generic CRUD
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';

    // Status
    case ACTIVATED = 'activated';
    case DEACTIVATED = 'deactivated';

    // News
    case BREAKING_MARKED = 'breaking_marked';
    case BREAKING_UNMARKED = 'breaking_unmarked';

    // Permissions & Roles
    case ROLE_ASSIGNED = 'role_assigned';
    case ROLE_REMOVED = 'role_removed';
    case PERMISSION_GRANTED = 'permission_granted';
    case PERMISSION_REVOKED = 'permission_revoked';

    // Files
    case FILE_UPLOADED = 'file_uploaded';
    case FILE_DELETED = 'file_deleted';

    // System
    case CREATED_BY_SYSTEM = 'created_by_system';
}
