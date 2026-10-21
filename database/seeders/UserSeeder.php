<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::findByName('admin', 'web');
        $supervisorRole = Role::findByName('supervisor', 'web');
        $userRole = Role::findByName('user', 'web');

        $user = User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@admin.com',
            'password' => 'password123',
            'status' => true,
        ]);

        $user->assignRole($adminRole);
        $supervisor = User::create([
            'name' => 'مشرف الموقع',
            'email' => 'super@admin.com',
            'password' => 'password123',
            'status' => true,
        ]);

        $supervisor->assignRole($supervisorRole);
        $normalUser = User::create([
            'name' => 'مستخدم تجريبي',
            'email' => 'user@user.com',
            'password' => 'password123',
            'status' => true,
        ]);

        $normalUser->assignRole($userRole);
    }
}
