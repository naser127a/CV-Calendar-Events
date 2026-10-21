<?php

namespace Tests\Unit;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_has_role_through_spatie_relationship(): void
    {
        // Arrange
        $role = Role::factory()->create([
            'name' => 'test-role',
        ]);

        $user = User::factory()->withRole($role)->create();

        $this->assertTrue(
            $user->hasRole($role)
        );
    }

    public function test_user_has_permission_through_role(): void
    {
        // Arrange
        $permission = Permission::factory()->create([
            'name' => 'test.permission',
        ]);

        $role = Role::factory()->create([
            'name' => 'test-role',
        ]);

        $role->permissions()->attach($permission);

        $user = User::factory()->withRole($role)->create();

        // Act
        $result = $user->can('test.permission');

        // Assert
        $this->assertTrue($result);
    }

    public function test_user_does_not_have_permission_when_role_does_not_have_it(): void
    {
        // Arrange
        $permission = Permission::factory()->create([
            'name' => 'test.permission',
        ]);

        $role = Role::factory()->create([
            'name' => 'test-role',
        ]);

        $user = User::factory()->withRole($role)->create();

        // Act
        $result = $user->can('test.permission');

        // Assert
        $this->assertFalse($result);
    }
}
