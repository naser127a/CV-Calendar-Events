<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::findByName('admin', 'web');

        $supervisor = Role::findByName(
            'supervisor',
            'web'
        );

        $user = Role::findByName(
            'user',
            'web'
        );

        $admin->syncPermissions(
            \App\Models\Permission::pluck('name')->all()
        );

        $supervisor->syncPermissions([
            'view_users',
            'create_users',
            'edit_users',

            'view_calendar',
            'create_calendar',
            'edit_calendar',

            'view_news',
            'create_news',
            'edit_news',

            'view_activity_log',
        ]);

        $user->syncPermissions([
            'view_calendar',
            'view_news',
        ]);
    }
}
