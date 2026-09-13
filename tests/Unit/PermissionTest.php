<?php

namespace Tests\Unit;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_has_roles_relationship(): void
    {
        $permission = Permission::factory()->create();

        $role = Role::factory()->create();

        $role->permissions()->attach($permission);

        $this->assertTrue(
            $permission->roles->contains($role)
        );
    }
    public function test_permission_can_be_assigned_to_role(): void
    {
        // Arrange
        $permission = Permission::factory()->create([
            'name' => 'test.permission',
        ]);

        $role = Role::factory()->create([
            'name' => 'test-role',
        ]);

        // Act
        $permission->roles()->attach($role);

        // Assert
        $this->assertTrue(
            $permission->roles->contains($role)
        );
    }

    public function test_permission_can_detach_from_role(): void
    {
        // Arrange
        $permission = Permission::factory()->create([
            'name' => 'test.permission',
        ]);

        $role = Role::factory()->create([
            'name' => 'test-role',
        ]);

        $permission->roles()->attach($role);

        // Act
        $permission->roles()->detach($role);

        // Assert
        $this->assertFalse(
            $permission->roles->contains($role)
        );
    }

    public function test_permission_belongs_to_multiple_roles(): void
    {
        // Arrange
        $permission = Permission::factory()->create([
            'name' => 'test.permission',
        ]);

        $admin = Role::factory()->create([
            'name' => 'test-admin',
        ]);

        $supervisor = Role::factory()->create([
            'name' => 'test-supervisor',
        ]);

        // Act
        $permission->roles()->attach([
            $admin->id,
            $supervisor->id,
        ]);

        // Assert
        $this->assertCount(2, $permission->roles);

        $this->assertTrue(
            $permission->roles->contains($admin)
        );

        $this->assertTrue(
            $permission->roles->contains($supervisor)
        );
    }
}
