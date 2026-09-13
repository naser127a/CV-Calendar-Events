<?php

namespace App\Services;

use App\Actions\Profile\UpdateProfileAction;
use App\DTOs\Profile\UpdateProfileData;
use App\Models\User;

class ProfileService
{
    public function __construct(
        private UpdateProfileAction $updateProfileAction
    ) {}
    public function me(User $user): User
    {
        return $user->load("role");
    }

    public function update(UpdateProfileData $data, User $user)
    {
        return $this->updateProfileAction->execute($data, $user);
    }
}
