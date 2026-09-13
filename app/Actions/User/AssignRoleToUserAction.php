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

        $user->assignRole($role->name);

        return $user->fresh('roles');
    }
}
