<?php

namespace Tests\Feature\Policies;

use App\Enums\ActivityLogPermissionEnum;
use App\Models\Role;
use App\Models\User;
use App\Policies\ActivityLogPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_user_with_view_all_permission_can_view_activity_logs(): void
    {
        $role = Role::where('name', 'admin')->firstOrFail();

        $permission = $role->permissions()
            ->where('name', ActivityLogPermissionEnum::VIEW_ALL->value)
            ->firstOrFail();

        $user = User::factory()->withRole($role)->create();

        $this->assertTrue(
            $user->can($permission->name)
        );

        $policy = new ActivityLogPolicy();

        $this->assertTrue(
            $policy->viewAny($user)
        );
    }

    public function test_user_without_view_all_permission_cannot_view_activity_logs(): void
    {
        $role = Role::where('name', 'user')->firstOrFail();

        $user = User::factory()->withRole($role)->create();

        $this->assertFalse(
            $user->can(
                ActivityLogPermissionEnum::VIEW_ALL->value
            )
        );

        $policy = new ActivityLogPolicy();

        $this->assertFalse(
            $policy->viewAny($user)
        );
    }
}
