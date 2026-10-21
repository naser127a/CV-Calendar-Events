<?php

namespace App\Actions\Auth;

use App\DTOs\RegisterData;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Traits\HandlesFileUpload;

class RegisterUserAction
{
    use HandlesFileUpload;

    public function __construct(
        private UserRepositoryInterface $users,
    ) {}

    public function execute(RegisterData $dto): User
    {
        $role = Role::findByName('user', 'web');

        $data = [
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password,
            'phone' => $dto->phone,
        ];

        if ($dto->avatar) {
            $data['avatar'] = $this->uploadFile(
                $dto->avatar,
                'avatars'
            );
        }

        $user = $this->users->create($data);

        $user->assignRole($role);
        return $user;
    }
}
