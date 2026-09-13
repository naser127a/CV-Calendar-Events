<?php

namespace App\Actions\User;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DeactivateUserAction
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UserRepositoryInterface $users
    ) {
        //
    }
    public function execute(User $user): User
    {

        if (Auth::id() === $user->id) {
            throw ValidationException::withMessages([
                'user' => 'لا يمكنك تعطيل حسابك.'
            ]);
        }
        return $this->users->update(
            $user,
            ['status' => false]
        );
    }
}
