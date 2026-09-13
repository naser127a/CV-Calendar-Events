<?php

namespace App\Services;

use App\DTOs\RegisterData;
use App\Actions\Auth\RegisterUserAction;
use App\Actions\Auth\ChangePasswordAction;
use App\Actions\Auth\LoginAction;
use App\Actions\Auth\LogoutAction;
use App\DTOs\ChangePasswordData;
use App\DTOs\LoginData;
use App\Events\User\UserLoggedIn;
use App\Events\User\UserRegistered;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthService
{
    public function __construct(
        private RegisterUserAction $createUserAction,
        private LoginAction $loginAction,
        private LogoutAction $logoutAction,
        private ChangePasswordAction $changePasswordAction,
    ) {}

    public function register(RegisterData $dto): array
    {
        return DB::transaction(function () use ($dto) {
            $user = $this->createUserAction->execute($dto);
            $token = $user->createToken('auth_token')->plainTextToken;
            UserRegistered::dispatch($user);
            // event(new UserRegistered($user));
            return [
                'user' => $user->load('roles'),
                'token' => $token,
            ];
        });
    }

    public function login(LoginData $credentials): array
    {
        return DB::transaction(function () use ($credentials) {
            $user = $this->loginAction->execute($credentials);
            UserLoggedIn::dispatch($user);
            $user->update([
                "last_login" => now()
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;
            return [
                'user' => $user->load('roles'),
                'token' => $token,
            ];
        });
    }

    public function me(User $user): User
    {
        return $user->load('roles');
    }

    public function logout(User $user): void
    {
        $this->logoutAction->execute($user);
    }
    public function logoutAll(User $user): void
    {
        $this->logoutAction->executeAll($user);
    }

    public function changePassword(User $user, ChangePasswordData $dto): void
    {
        $this->changePasswordAction->execute($user, $dto);
    }
}
