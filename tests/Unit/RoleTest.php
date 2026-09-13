<?php

namespace Tests\Unit;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_has_users_relationship(): void
    {
        $role = Role::factory()->create();

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $this->assertTrue(
            $role->users->contains($user)
        );
    }

    public function test_role_has_permissions_relationship(): void
    {
        $role = Role::factory()->create();

        $permission = Permission::factory()->create();

        $role->permissions()->attach($permission);

        $this->assertTrue(
            $role->permissions->contains($permission)
        );
    }

    public function test_role_has_permission_returns_true_when_permission_exists(): void
    {
        $role = Role::factory()->create();

        $permission = Permission::factory()->create([
            'name' => 'users.view',
        ]);

        $role->permissions()->attach($permission);

        $this->assertTrue(
            $role->can('users.view')
        );
    }

    public function test_role_has_permission_returns_false_when_permission_does_not_exist(): void
    {
        $role = Role::factory()->create();

        $this->assertFalse(
            $role->can('users.delete')
        );
    }

    public function test_role_can_be_assigned_permission(): void
    {
        // Arrange
        $role = Role::factory()->create();

        $permission = Permission::factory()->create([
            'name' => 'users.view',
        ]);

        // Act
        $role->permissions()->attach($permission);

        // Assert
        $this->assertTrue(
            $role->permissions->contains($permission)
        );
    }
    public function test_role_can_attach_permission(): void
    {
        // Arrange
        $role = Role::factory()->create([
            'name' => 'test-role',
        ]);

        $permission = Permission::factory()->create([
            'name' => 'test.permission',
        ]);

        // Act
        $role->permissions()->attach($permission);

        // Assert
        $this->assertTrue(
            $role->permissions->contains($permission)
        );
    }
    public function test_role_can_detach_permission(): void
    {
        // Arrange
        $role = Role::factory()->create([
            'name' => 'test-role',
        ]);

        $permission = Permission::factory()->create([
            'name' => 'test.permission',
        ]);

        $role->permissions()->attach($permission);

        // Act
        $role->permissions()->detach($permission);

        // Assert
        $this->assertFalse(
            $role->permissions->contains($permission)
        );
    }
}
