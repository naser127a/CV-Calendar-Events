<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PermissionApiTest extends TestCase
{
    use RefreshDatabase;
    public function test_authorized_user_can_view_permissions(): void
    {
        $admin = User::where('email', 'admin@admin.com')->firstOrFail();

        Permission::factory()->create([
            'name' => 'news.view',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/permissions');

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data',
        ]);
    }
    public function test_unauthenticated_user_cannot_view_permissions(): void
{
    $response = $this->getJson('/api/v1/permissions');

    $response->assertUnauthorized();
}
public function test_user_without_permission_cannot_view_permissions(): void
{
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/permissions');

    $response->assertForbidden();
}
public function test_authorized_user_can_create_permission(): void
{
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    Sanctum::actingAs($admin);

    $data = [
        'name' => 'news.publish',
        'display_name' => 'نشر الأخبار',
        'description' => 'السماح بنشر الأخبار',
    ];

    $response = $this->postJson(
        '/api/v1/permissions',
        $data
    );

    $response->assertSuccessful();

    $this->assertDatabaseHas('permissions', [
        'name' => 'news.publish',
        'display_name' => 'نشر الأخبار',
    ]);
}

public function test_create_permission_requires_name(): void
{
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    Sanctum::actingAs($admin);

    $response = $this->postJson('/api/v1/permissions', [
        'display_name' => 'صلاحية بدون اسم',
    ]);

    $response->assertUnprocessable();

    $response->assertJsonValidationErrors([
        'name',
    ]);
}
public function test_create_permission_rejects_duplicate_name(): void
{
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    Permission::factory()->create([
        'name' => 'news.publish',
    ]);

    Sanctum::actingAs($admin);

    $response = $this->postJson('/api/v1/permissions', [
        'name' => 'news.publish',
        'display_name' => 'نشر الأخبار',
        'description' => 'اختبار',
    ]);

    $response->assertUnprocessable();

    $response->assertJsonValidationErrors([
        'name',
    ]);
}
public function test_authorized_user_can_view_single_permission(): void
{
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    $permission = Permission::factory()->create([
        'name' => 'news.publish',
        'display_name' => 'نشر الأخبار',
    ]);

    Sanctum::actingAs($admin);

    $response = $this->getJson(
        "/api/v1/permissions/{$permission->id}"
    );

    $response->assertSuccessful();

    $response->assertJsonPath(
        'data.id',
        $permission->id
    );

    $response->assertJsonPath(
        'data.name',
        'news.publish'
    );
}

public function test_view_single_permission_returns_not_found_for_missing_permission(): void
{
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    Sanctum::actingAs($admin);

    $response = $this->getJson(
        '/api/v1/permissions/999999'
    );

    $response->assertNotFound();
}
public function test_authorized_user_can_update_permission(): void
{
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    $permission = Permission::factory()->create([
        'name' => 'news.publish',
        'display_name' => 'نشر الأخبار',
    ]);

    Sanctum::actingAs($admin);

    $response = $this->putJson(
        "/api/v1/permissions/{$permission->id}",
        [
            'name' => 'news.approve',
            'display_name' => 'اعتماد الأخبار',
            'description' => 'صلاحية اعتماد الأخبار',
        ]
    );

    $response->assertSuccessful();

    $this->assertDatabaseHas('permissions', [
        'id' => $permission->id,
        'name' => 'news.approve',
        'display_name' => 'اعتماد الأخبار',
    ]);
}

public function test_authorized_user_can_delete_permission(): void
{
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    $permission = Permission::factory()->create([
        'name' => 'news.temporary',
    ]);

    Sanctum::actingAs($admin);

    $response = $this->deleteJson(
        "/api/v1/permissions/{$permission->id}"
    );

    $response->assertSuccessful();

    $this->assertDatabaseMissing('permissions', [
        'id' => $permission->id,
    ]);
}
}
