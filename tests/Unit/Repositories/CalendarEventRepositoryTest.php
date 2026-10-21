<?php

namespace Tests\Unit\Repositories;

use App\DTOs\Calendar\CalendarFilterData;
use App\Enums\CalendarPermissionEnum;
use App\Models\CalendarEvent;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Eloquent\CalendarEventRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarEventRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_view_all_permission_only_sees_own_events(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        CalendarEvent::factory()->create(['created_by' => $user->id]);
        CalendarEvent::factory()->create(['created_by' => $other->id]);

        $results = app(CalendarEventRepository::class)->search(
            $this->filters(),
            $user
        );

        $this->assertSame([$user->id], $results->getCollection()->pluck('created_by')->all());
        $this->assertSame(1, $results->total());
    }

    public function test_view_all_permission_allows_viewing_events_from_all_users(): void
    {
        $permission = Permission::factory()->create([
            'name' => CalendarPermissionEnum::VIEW_ALL->value,
        ]);
        $role = Role::factory()->create();
        $role->givePermissionTo($permission);
        $user = User::factory()->create();
        $user->assignRole($role);

        CalendarEvent::factory()->count(2)->create();
        $other = User::factory()->create();
        CalendarEvent::factory()->create(['created_by' => $other->id]);

        $results = app(CalendarEventRepository::class)->search(
            $this->filters(),
            $user
        );

        $this->assertSame(3, $results->total());
    }

    public function test_admin_role_name_without_view_all_permission_does_not_grant_global_visibility(): void
    {
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $admin = User::factory()->withRole($adminRole)->create();
        CalendarEvent::factory()->create(['created_by' => $admin->id]);
        CalendarEvent::factory()->create();

        $results = app(CalendarEventRepository::class)->search(
            $this->filters(),
            $admin
        );

        $this->assertSame(1, $results->total());
    }

    public function test_upcoming_events_are_scoped_to_the_user(): void
    {
        $user = User::factory()->create();
        CalendarEvent::factory()->create([
            'created_by' => $user->id,
            'start_at' => now()->addDay(),
        ]);
        CalendarEvent::factory()->create([
            'start_at' => now()->addDay(),
        ]);

        $results = app(CalendarEventRepository::class)->getUpcoming($user);

        $this->assertCount(1, $results);
        $this->assertSame($user->id, $results->first()->created_by);
    }

    private function filters(): CalendarFilterData
    {
        return new CalendarFilterData(
            search: null,
            status: null,
            color: null,
            sort_by: 'id',
            sort_dir: 'asc',
            per_page: 100
        );
    }
}
