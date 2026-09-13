<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::findOrCreate('admin', 'web');

        $supervisor = Role::findOrCreate('supervisor', 'web');

        $user = Role::findOrCreate('user', 'web');
    }
}
