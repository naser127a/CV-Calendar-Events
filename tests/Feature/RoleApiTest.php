<?php

namespace Tests\Feature;

use App\Enums\RolePermissionEnum;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleApiTest extends TestCase
{
    use RefreshDatabase;
    public function test_authorized_user_can_view_roles(): void
    {
        // Arrange
        $admin = User::where('email', 'admin@admin.com')->firstOrFail();

        Role::factory()->count(3)->create();

        Sanctum::actingAs($admin);

        // Act
        $response = $this->getJson('/api/v1/roles');

        // Assert
        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data',
        ]);
    }

    public function test_unauthenticated_user_cannot_view_roles(): void
    {
        // Arrange
        // لا يوجد User ولا تسجيل دخول

        // Act
        $response = $this->getJson('/api/v1/roles');

        // Assert
        $response->assertUnauthorized();
    }

    public function test_user_without_permission_cannot_view_roles(): void
    {
        // Arrange
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        // Act
        $response = $this->getJson('/api/v1/roles');

        // Assert
        $response->assertForbidden();
    }

    public function test_authorized_user_can_create_role(): void
    {
        // Arrange
        $admin = User::where('email', 'admin@admin.com')->firstOrFail();

        Sanctum::actingAs($admin);

        $data = [
            'name' => 'editor',
            'display_name' => 'محرر',
            'description' => 'دور المحرر',
        ];

        // Act
        $response = $this->postJson('/api/v1/roles', $data);

        // Assert
        $response->assertSuccessful();

        $this->assertDatabaseHas('roles', [
            'name' => 'editor',
            'display_name' => 'محرر',
        ]);
    }
    public function test_create_role_requires_name(): void
    {
        $admin = User::where('email', 'admin@admin.com')->firstOrFail();

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/roles', [
            'display_name' => 'دور بدون اسم',
            'description' => 'اختبار',
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'name',
        ]);
    }
    public function test_create_role_rejects_duplicate_name(): void
    {
        $admin = User::where('email', 'admin@admin.com')->firstOrFail();

        Sanctum::actingAs($admin);

        Role::factory()->create([
            'name' => 'editor',
        ]);

        $response = $this->postJson('/api/v1/roles', [
            'name' => 'editor',
            'display_name' => 'محرر',
            'description' => 'دور المحرر',
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'name',
        ]);
    }

    public function test_authorized_user_can_view_single_role(): void
{
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    $role = Role::factory()->create([
        'name' => 'editor',
        'display_name' => 'محرر',
    ]);

    Sanctum::actingAs($admin);

    $response = $this->getJson(
        "/api/v1/roles/{$role->id}"
    );

    $response->assertSuccessful();

    $response->assertJsonPath(
        'data.id',
        $role->id
    );

    $response->assertJsonPath(
        'data.name',
        'editor'
    );
}

public function test_view_single_role_returns_not_found_for_missing_role(): void
{
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    Sanctum::actingAs($admin);

    $response = $this->getJson('/api/v1/roles/999999');

    $response->assertNotFound();
}

public function test_authorized_user_can_update_role(): void
{
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    $role = Role::factory()->create([
        'name' => 'editor',
        'display_name' => 'محرر',
    ]);

    Sanctum::actingAs($admin);

    $response = $this->putJson(
        "/api/v1/roles/{$role->id}",
        [
            'name' => 'senior-editor',
            'display_name' => 'محرر أول',
            'description' => 'دور محدث',
        ]
    );

    $response->assertSuccessful();

    $this->assertDatabaseHas('roles', [
        'id' => $role->id,
        'name' => 'senior-editor',
        'display_name' => 'محرر أول',
    ]);
}

public function test_authorized_user_can_delete_role(): void
{
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    $role = Role::factory()->create([
        'name' => 'temporary-role',
    ]);

    Sanctum::actingAs($admin);

    $response = $this->deleteJson(
        "/api/v1/roles/{$role->id}"
    );

    $response->assertSuccessful();

    $this->assertDatabaseMissing('roles', [
        'id' => $role->id,
    ]);
}

public function test_authorized_user_can_view_role_permissions(): void
{
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    $role = Role::factory()->create([
        'name' => 'editor',
    ]);

    $permission = Permission::factory()->create([
        'name' => 'news.view',
    ]);

    $role->permissions()->attach($permission);

    Sanctum::actingAs($admin);

    $response = $this->getJson(
        "/api/v1/roles/{$role->id}/permissions"
    );

    $response->assertSuccessful();

    $response->assertJsonFragment([
        'name' => 'news.view',
    ]);
}

public function test_authorized_user_can_attach_permission_to_role(): void
{
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    $role = Role::factory()->create([
        'name' => 'editor',
    ]);

    $permission = Permission::factory()->create([
        'name' => 'news.view',
    ]);

    Sanctum::actingAs($admin);

    $response = $this->postJson(
        "/api/v1/roles/{$role->id}/permissions/attach",
        [
            'permissions' => [$permission->id],
        ]
    );

    $response->assertSuccessful();

    $this->assertDatabaseHas('permission_has_role', [
        'role_id' => $role->id,
        'permission_id' => $permission->id,
    ]);
}

public function test_authorized_user_can_detach_permission_from_role(): void
{
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    $role = Role::factory()->create([
        'name' => 'editor',
    ]);

    $permission = Permission::factory()->create([
        'name' => 'news.view',
    ]);

    $role->permissions()->attach($permission);

    Sanctum::actingAs($admin);

    $response = $this->deleteJson(
        "/api/v1/roles/{$role->id}/permissions/detach",
        [
            'permissions' => [$permission->id],
        ]
    );

    $response->assertSuccessful();

    $this->assertDatabaseMissing('permission_has_role', [
        'role_id' => $role->id,
        'permission_id' => $permission->id,
    ]);
}

public function test_authorized_user_can_sync_role_permissions(): void
{
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    $role = Role::factory()->create([
        'name' => 'editor',
    ]);

    $permissionA = Permission::factory()->create([
        'name' => 'news.view',
    ]);

    $permissionB = Permission::factory()->create([
        'name' => 'news.create',
    ]);

    $permissionC = Permission::factory()->create([
        'name' => 'news.update',
    ]);

    $role->permissions()->attach([
        $permissionA->id,
        $permissionB->id,
    ]);

    Sanctum::actingAs($admin);

    $response = $this->postJson(
        "/api/v1/roles/{$role->id}/permissions/sync",
        [
            'permissions' => [
                $permissionA->id,
                $permissionC->id,
            ],
        ]
    );

    $response->assertSuccessful();

    $this->assertDatabaseHas('permission_has_role', [
        'role_id' => $role->id,
        'permission_id' => $permissionA->id,
    ]);

    $this->assertDatabaseHas('permission_has_role', [
        'role_id' => $role->id,
        'permission_id' => $permissionC->id,
    ]);

    $this->assertDatabaseMissing('permission_has_role', [
        'role_id' => $role->id,
        'permission_id' => $permissionB->id,
    ]);
}
}
