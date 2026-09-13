<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'token',
                ],
            ]);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'login1@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'login1@gmail.com',
            'password' => 'wrongpassword',
        ]);

        // $response->assertUnauthorized();
        $response->assertUnprocessable();
        $response->assertJsonPath(
    'errors.email.0',
    'The provided credentials are incorrect.'
);
    }

    public function test_user_cannot_login_with_unknown_email(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'unknown@example.com',
            'password' => 'password123',
        ]);

          $response->assertUnprocessable();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/logout');

        $response->assertSuccessful();
    }

    public function test_user_can_view_authenticated_profile(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/me');

        $response
            ->assertSuccessful()
            ->assertJsonPath('data.id', $user->id);
    }
}
