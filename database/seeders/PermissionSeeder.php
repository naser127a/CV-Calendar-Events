<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Support\PermissionRegistry;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PermissionRegistry::enums() as $enumClass) {

            foreach ($enumClass::cases() as $permission) {

                Permission::firstOrCreate(
                    [
                        'name' => $permission->value,
                    ],
                    [
                        "guard_name" => "web"
                    ]
                );
            }
        }
    }
}
