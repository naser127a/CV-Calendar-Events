<?php

namespace App\Actions\User;

use App\Models\Role;
use App\Models\User;

class AssignRoleToUserAction
{
    public function execute(
        User $user,
        Role $role
    ): User {
        $user->syncRoles([$role]);

        return $user->fresh('roles');
    }
}
