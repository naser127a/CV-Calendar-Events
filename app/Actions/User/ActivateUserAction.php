<?php

namespace App\Actions\User;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class ActivateUserAction
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UserRepositoryInterface $users
    ) {
        //
    }
    public function execute(User $user)
    {
        return $this->users->update(
            $user,
            ['status' => true]
        );
    }
}
