<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_user_can_register(): void
    {
        $role = Role::where('name', 'user')->firstOrFail();

        $response = $this->postJson('/api/v1/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $role->id,
        ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
        ]);
    }

    public function test_registration_requires_name(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_registration_requires_unique_email(): void
    {
        $this->postJson('/api/v1/register', [
            'name' => 'User One',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response = $this->postJson('/api/v1/register', [
            'name' => 'User Two',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_registration_requires_matching_password_confirmation(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('password');
    }
}
