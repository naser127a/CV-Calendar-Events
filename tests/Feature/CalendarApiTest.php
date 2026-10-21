<?php

namespace Tests\Feature;

use App\Enums\CalendarPermissionEnum;
use App\Models\CalendarEvent;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CalendarApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_list_only_returns_events_owned_by_user_without_view_all(): void
    {
        $user = $this->userWithPermission(CalendarPermissionEnum::VIEW);
        $ownEvent = CalendarEvent::factory()->create(['created_by' => $user->id]);
        CalendarEvent::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/events');

        $response->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ownEvent->id);
    }

    public function test_view_all_permission_allows_viewing_events_owned_by_others(): void
    {
        $user = $this->userWithPermission(CalendarPermissionEnum::VIEW_ALL);
        CalendarEvent::factory()->count(2)->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/events')
            ->assertSuccessful()
            ->assertJsonCount(2, 'data');
    }

    public function test_create_uses_authenticated_user_instead_of_submitted_creator(): void
    {
        $user = $this->userWithPermission(CalendarPermissionEnum::CREATE);
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/events', [
            'title' => 'Trusted creator',
            'start_at' => now()->addDay()->toDateTimeString(),
            'created_by' => $otherUser->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('calendar_events', [
            'title' => 'Trusted creator',
            'created_by' => $user->id,
        ]);
        $this->assertDatabaseMissing('calendar_events', [
            'title' => 'Trusted creator',
            'created_by' => $otherUser->id,
        ]);
    }

    public function test_update_of_another_users_event_is_forbidden_without_manage_all(): void
    {
        $user = $this->userWithPermission(CalendarPermissionEnum::UPDATE);
        $event = CalendarEvent::factory()->create();
        Sanctum::actingAs($user);

        $this->putJson("/api/v1/events/{$event->id}", [
            'title' => 'Unauthorized update',
        ])->assertForbidden();

        $this->assertNotSame('Unauthorized update', $event->fresh()->title);
    }

    public function test_manage_all_does_not_replace_operation_permission(): void
    {
        $user = $this->userWithPermission(CalendarPermissionEnum::MANAGE_ALL);
        $event = CalendarEvent::factory()->create();
        Sanctum::actingAs($user);

        $this->putJson("/api/v1/events/{$event->id}", [
            'title' => 'No update permission',
        ])->assertForbidden();
    }

    public function test_filter_rejects_invalid_status_and_page_size(): void
    {
        $user = $this->userWithPermission(CalendarPermissionEnum::VIEW);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/events?status=maybe')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->getJson('/api/v1/events?per_page=2.5')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('per_page');

        $this->getJson('/api/v1/events?per_page=0')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('per_page');
    }

    private function userWithPermission(CalendarPermissionEnum $permission): User
    {
        $permissionModel = Permission::factory()->create([
            'name' => $permission->value,
        ]);
        $role = Role::factory()->create();
        $role->givePermissionTo($permissionModel);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
