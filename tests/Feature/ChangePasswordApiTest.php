<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChangePasswordApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Sanctum::actingAs($this->user);
    }

    public function test_authenticated_user_can_change_password(): void
    {
        $response = $this->patchJson('/api/v1/changePassword-password', [
            'current_password' => 'old-password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertSuccessful();

        $this->user->refresh();

        $this->assertTrue(
            Hash::check(
                'new-password123',
                $this->user->password
            )
        );
    }

    public function test_user_cannot_change_password_with_wrong_current_password(): void
    {
        $response = $this->patchJson('/api/v1/changePassword-password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertUnprocessable();
    }
}
