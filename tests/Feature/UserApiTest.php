<?php

namespace Tests\Feature;

use App\Models\Permission;
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

        $this->admin = User::factory()->create([
            'role_id' => $this->adminRole->id,
        ]);

        Sanctum::actingAs($this->admin);
    }

    /**
     * عرض قائمة المستخدمين.
     */
    public function test_authorized_user_can_view_users(): void
    {
        User::factory()->count(3)->create([
            'role_id' => $this->userRole->id,
        ]);

        $response = $this->getJson('/api/v1/users');

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data',
            ]);
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

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

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
            'role_id' => $this->userRole->id,
            "status"=>true
        ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'role_id' => $this->userRole->id,
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
            'role_id' => $this->userRole->id,
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
            'role_id' => $this->userRole->id,
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
        User::factory()->create([
            'email' => 'existing@example.com',
            'role_id' => $this->userRole->id,
        ]);

        $response = $this->postJson('/api/v1/users', [
            'name' => 'Another User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $this->userRole->id,
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
        $user = User::factory()->create([
            'role_id' => $this->userRole->id,
        ]);

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
        $user = User::factory()->create([
            'name' => 'Old Name',
            'role_id' => $this->userRole->id,
        ]);

        $response = $this->putJson("/api/v1/users/{$user->id}", [
            'name' => 'New Name',
            'email' => $user->email,
            'phone' => $user->phone,
            'role_id' => $this->userRole->id,
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
        $user = User::factory()->create([
            'role_id' => $this->userRole->id,
        ]);

        $response = $this->deleteJson("/api/v1/users/{$user->id}");

        $response->assertSuccessful();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            "deleted_at"=>null
        ]);
    }

    /**
     * تفعيل مستخدم.
     */
    public function test_authorized_user_can_activate_user(): void
    {
        $user = User::factory()->create([
            'role_id' => $this->userRole->id,
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
        $user = User::factory()->create([
            'role_id' => $this->userRole->id,
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

        $user = User::factory()->create([
            'role_id' => $this->userRole->id,
        ]);

        $response = $this->patchJson(
            "/api/v1/users/{$user->id}/change-role",
            [
                'role_id' => $newRole->id,
            ]
        );

        $response->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role_id' => $newRole->id,
        ]);
    }
}
