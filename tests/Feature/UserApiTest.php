<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Role $adminRole;

    protected Role $userRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->adminRole = Role::where('name', 'admin')->firstOrFail();

        $this->userRole = Role::where('name', 'user')->firstOrFail();

        $this->admin = User::factory()->withRole($this->adminRole)->create();

        Sanctum::actingAs($this->admin);
    }

    /**
     * عرض قائمة المستخدمين.
     */
    public function test_authorized_user_can_view_users(): void
    {
        User::factory()->count(3)->withRole($this->userRole)->create();

        $response = $this->getJson('/api/v1/users');

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_user_list_is_not_filtered_by_status_when_status_is_omitted(): void
    {
        $activeUser = User::factory()->withRole($this->userRole)->create([
            'name' => 'Active user',
            'status' => true,
        ]);
        $inactiveUser = User::factory()->withRole($this->userRole)->create([
            'name' => 'Inactive user',
            'status' => false,
        ]);

        $this->getJson('/api/v1/users')
            ->assertSuccessful()
            ->assertJsonFragment(['id' => $activeUser->id])
            ->assertJsonFragment(['id' => $inactiveUser->id]);
    }

    /**
     * المستخدم غير المسجل لا يستطيع عرض المستخدمين.
     */
    public function test_unauthenticated_user_cannot_view_users(): void
    {
        $this->app['auth']->forgetGuards();

        $response = $this->getJson('/api/v1/users');

        $response->assertUnauthorized();
    }

    /**
     * مستخدم بدون صلاحية عرض المستخدمين.
     */
    public function test_user_without_permission_cannot_view_users(): void
    {
        $role = Role::factory()->create();

        $user = User::factory()->withRole($role)->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/users');

        $response->assertForbidden();
    }

    /**
     * إنشاء مستخدم جديد.
     */
    public function test_authorized_user_can_create_user(): void
    {
        $response = $this->postJson('/api/v1/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0500000001',
            'roleName' => $this->userRole->name,
            'status' => true,
        ]);

        $response->assertSuccessful();

        $createdUser = User::where('email', 'test@example.com')->firstOrFail();

        $this->assertTrue($createdUser->hasRole($this->userRole));
    }

    public function test_user_with_create_permission_cannot_create_admin_user(): void
    {
        $supervisor = User::where('email', 'super@admin.com')->firstOrFail();
        Sanctum::actingAs($supervisor);

        $response = $this->postJson('/api/v1/users', [
            'name' => 'Unauthorized Admin',
            'email' => 'unauthorized-admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roleName' => $this->adminRole->name,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('users', [
            'email' => 'unauthorized-admin@example.com',
        ]);
    }

    /**
     * الاسم مطلوب.
     */
    public function test_create_user_requires_name(): void
    {
        $response = $this->postJson('/api/v1/users', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roleName' => $this->userRole->name,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    /**
     * البريد الإلكتروني مطلوب.
     */
    public function test_create_user_requires_email(): void
    {
        $response = $this->postJson('/api/v1/users', [
            'name' => 'Test User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roleName' => $this->userRole->name,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    /**
     * لا يمكن إنشاء مستخدم ببريد مكرر.
     */
    public function test_create_user_rejects_duplicate_email(): void
    {
        User::factory()->withRole($this->userRole)->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->postJson('/api/v1/users', [
            'name' => 'Another User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roleName' => $this->userRole->name,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    /**
     * عرض مستخدم واحد.
     */
    public function test_authorized_user_can_view_single_user(): void
    {
        $user = User::factory()->withRole($this->userRole)->create();

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response
            ->assertSuccessful()
            ->assertJsonPath('data.id', $user->id);
    }

    /**
     * المستخدم غير الموجود يرجع 404.
     */
    public function test_view_single_user_returns_not_found_for_missing_user(): void
    {
        $response = $this->getJson('/api/v1/users/999999');

        $response->assertNotFound();
    }

    /**
     * تعديل مستخدم.
     */
    public function test_authorized_user_can_update_user(): void
    {
        $user = User::factory()->withRole($this->userRole)->create([
            'name' => 'Old Name',
        ]);

        $response = $this->putJson("/api/v1/users/{$user->id}", [
            'name' => 'New Name',
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => true,
        ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);
    }

    /**
     * حذف مستخدم.
     */
    public function test_authorized_user_can_delete_user(): void
    {
        $user = User::factory()->withRole($this->userRole)->create();

        $response = $this->deleteJson("/api/v1/users/{$user->id}");

        $response->assertSuccessful();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * تفعيل مستخدم.
     */
    public function test_authorized_user_can_activate_user(): void
    {
        $user = User::factory()->withRole($this->userRole)->create([
            'status' => false,
        ]);

        $response = $this->patchJson(
            "/api/v1/users/{$user->id}/activate"
        );

        $response->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => true,
        ]);
    }

    /**
     * تعطيل مستخدم.
     */
    public function test_authorized_user_can_deactivate_user(): void
    {
        $user = User::factory()->withRole($this->userRole)->create([
            'status' => true,
        ]);

        $response = $this->patchJson(
            "/api/v1/users/{$user->id}/deactivate"
        );

        $response->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => false,
        ]);
    }

    /**
     * تغيير دور المستخدم.
     */
    public function test_authorized_user_can_change_user_role(): void
    {
        $newRole = Role::where('name', 'supervisor')->firstOrFail();

        $user = User::factory()->withRole($this->userRole)->create();

        $response = $this->patchJson(
            "/api/v1/users/{$user->id}/change-role",
            [
                'roleName' => $newRole->name,
            ]
        );

        $response->assertSuccessful();

        $this->assertTrue($user->fresh()->hasRole($newRole));
    }

    public function test_user_with_edit_permission_cannot_promote_user_to_admin(): void
    {
        $supervisor = User::where('email', 'super@admin.com')->firstOrFail();
        $user = User::factory()->withRole($this->userRole)->create();
        Sanctum::actingAs($supervisor);

        $response = $this->patchJson(
            "/api/v1/users/{$user->id}/change-role",
            ['roleName' => $this->adminRole->name]
        );

        $response->assertForbidden();
        $this->assertTrue($user->fresh()->hasRole($this->userRole));
    }

    public function test_user_without_view_permission_cannot_list_trashed_users(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/users/trashed')->assertForbidden();
    }

    public function test_user_without_delete_permission_cannot_soft_delete_user(): void
    {
        $user = User::factory()->withRole($this->userRole)->create();
        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson("/api/v1/users/{$user->id}/soft-delete")
            ->assertForbidden();

        $this->assertNotSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_user_without_update_permission_cannot_restore_user(): void
    {
        $user = User::factory()->withRole($this->userRole)->create();
        $user->delete();
        Sanctum::actingAs(User::factory()->create());

        $this->postJson("/api/v1/users/{$user->id}/restore")
            ->assertForbidden();

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_user_without_delete_permission_cannot_force_delete_user(): void
    {
        $user = User::factory()->withRole($this->userRole)->create();
        $user->delete();
        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson("/api/v1/users/{$user->id}/force-delete")
            ->assertForbidden();

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_admin_can_list_trashed_users_and_restore_them(): void
    {
        $trashedUser = User::factory()->withRole($this->userRole)->create();
        $trashedUser->delete();

        $this->getJson('/api/v1/users/trashed')
            ->assertSuccessful()
            ->assertJsonFragment(['id' => $trashedUser->id]);

        $this->postJson("/api/v1/users/{$trashedUser->id}/restore")
            ->assertSuccessful();

        $this->assertNotSoftDeleted('users', ['id' => $trashedUser->id]);
    }
}
