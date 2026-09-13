<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->user = User::factory()->create();

        Sanctum::actingAs($this->user);
    }

    public function test_authenticated_user_can_view_profile(): void
    {
        $response = $this->getJson('/api/v1/profile');

        $response
            ->assertSuccessful()
            ->assertJsonPath('data.id', $this->user->id);
    }

    public function test_unauthenticated_user_cannot_view_profile(): void
    {
        $this->app['auth']->forgetGuards();

        $response = $this->getJson('/api/v1/profile');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_update_profile(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->patchJson('/api/v1/profile', [
                'name' => 'Updated Name',
                'email' => $this->user->email,
                'phone' => '0500000000',
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'phone' => '0500000000',
        ]);
    }
}
