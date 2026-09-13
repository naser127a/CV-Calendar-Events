<?php

namespace App\Actions\Auth;

use App\DTOs\LoginData;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginAction
{
    public function __construct() {}

    public function execute(LoginData $credentials): User
    {
        if (!Auth::attempt([
            "email" => $credentials->email,
            "password" => $credentials->password
        ])) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
