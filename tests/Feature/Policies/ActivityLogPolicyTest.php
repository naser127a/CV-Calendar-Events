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

    public function test_user_with_view_all_permission_can_view_activity_logs(): void
    {
        $role = Role::where('name', 'admin')->firstOrFail();

        $permission = $role->permissions()
            ->where('name', ActivityLogPermissionEnum::VIEW_ALL->value)
            ->firstOrFail();

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

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

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

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
