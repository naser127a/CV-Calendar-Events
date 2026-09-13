<?php

namespace App\Actions\User;

use App\DTOs\User\UpdateUserData;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Traits\HandlesFileUpload;

class UpdateUserAction
{
    use HandlesFileUpload;
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UserRepositoryInterface $users
    ) {
        //
    }
    public function execute(User $user, UpdateUserData $dto): User
    {
        $avatar=null;
        if ($dto->avatar) {
            $avatar = $this->uploadFile($dto->avatar, 'avatars');
        }
        $data = array_filter([
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'avatar' => $avatar,
        ], fn($value) => $value !== null);

        return $this->users->update($user, $data);
    }
}
