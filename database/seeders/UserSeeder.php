<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {

        $user = User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@admin.com',
            'password' => 'password123',
            'status' => true,
        ]);

        $user->assignRole('admin');
        $supervisor = User::create([
            'name' => 'مشرف الموقع',
            'email' => 'super@admin.com',
            'password' => 'password123',
            'status' => true,
        ]);

        $supervisor->assignRole('supervisor');
        $normalUser = User::create([
            'name' => 'مستخدم تجريبي',
            'email' => 'user@user.com',
            'password' => 'password123',
            'status' => true,
        ]);

        $normalUser->assignRole('user');
    }
}
