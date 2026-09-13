<?php

namespace App\Actions\Auth;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class LogoutAction
{
    public function execute(User $user): void
    {
        /** @var PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();

        if ($token !== null) {
            $token->delete();
        }
    }

    public function executeAll(User $user): void
    {
        $user->tokens()->delete();
    }
}
