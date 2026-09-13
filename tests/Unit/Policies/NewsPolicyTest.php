<?php

namespace Tests\Unit\Policies;

use App\Enums\NewsPermissionEnum;
use App\Models\News;
use App\Models\User;
use App\Policies\NewsPolicy;
use Tests\TestCase;

class NewsPolicyTest extends TestCase
{
    private NewsPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = app(NewsPolicy::class);
    }

    public function test_user_with_view_all_permission_can_view_any_news(): void
    {
        $user = User::factory()->create();

        $user->role->permissions()->attach(
            $this->permission(NewsPermissionEnum::VIEW_ALL->value)
        );

        $this->assertTrue(
            $this->policy->viewAny($user)
        );
    }

    public function test_user_without_view_all_permission_cannot_view_any_news(): void
    {
        $user = User::factory()->create();

        $this->assertFalse(
            $this->policy->viewAny($user)
        );
    }

    public function test_user_with_view_permission_can_view_news(): void
    {
        $user = User::factory()->create();

        $user->role->permissions()->attach(
            $this->permission(NewsPermissionEnum::VIEW->value)
        );

        $news = News::factory()->create();

        $this->assertTrue(
            $this->policy->view($user, $news)
        );
    }

    public function test_user_without_view_permission_cannot_view_news(): void
    {
        $user = User::factory()->create();
        $news = News::factory()->create();

        $this->assertFalse(
            $this->policy->view($user, $news)
        );
    }

    public function test_user_with_create_permission_can_create_news(): void
    {
        $user = User::factory()->create();

        $user->role->permissions()->attach(
            $this->permission(NewsPermissionEnum::CREATE->value)
        );

        $this->assertTrue(
            $this->policy->create($user)
        );
    }

    public function test_user_with_update_permission_can_update_news(): void
    {
        $user = User::factory()->create();

        $user->role->permissions()->attach(
            $this->permission(NewsPermissionEnum::UPDATE->value)
        );

        $news = News::factory()->create();

        $this->assertTrue(
            $this->policy->update($user, $news)
        );
    }

    public function test_user_with_delete_permission_can_delete_news(): void
    {
        $user = User::factory()->create();

        $user->role->permissions()->attach(
            $this->permission(NewsPermissionEnum::DELETE->value)
        );

        $news = News::factory()->create();

        $this->assertTrue(
            $this->policy->delete($user, $news)
        );
    }

    public function test_user_with_activate_permission_can_activate_news(): void
    {
        $user = User::factory()->create();

        $user->role->permissions()->attach(
            $this->permission(NewsPermissionEnum::ACTIVATE->value)
        );

        $news = News::factory()->create([
            'status' => false,
        ]);

        $this->assertTrue(
            $this->policy->activate($user, $news)
        );
    }

    public function test_user_with_deactivate_permission_can_deactivate_news(): void
    {
        $user = User::factory()->create();

        $user->role->permissions()->attach(
            $this->permission(NewsPermissionEnum::DEACTIVATE->value)
        );

        $news = News::factory()->create([
            'status' => true,
        ]);

        $this->assertTrue(
            $this->policy->deactivate($user, $news)
        );
    }

    private function permission(string $name)
    {
        return \App\Models\Permission::factory()->create([
            'name' => $name,
        ]);
    }
}
