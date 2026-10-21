<?php

namespace App\Actions\User;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\DTOs\User\CreateUserData;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;
use App\Traits\HandlesFileUpload;

class CreateUserAction
{
    use HandlesFileUpload;
    public function __construct(
        private UserRepositoryInterface $users,
    ) {}

    public function execute(CreateUserData $dto): User
    {
        $role = Role::findByName($dto->roleName ?? 'user', 'web');
        Gate::authorize('assignRole', [User::class, $role]);

        $avatar = null;
        if ($dto->avatar) {
            $avatar = $this->uploadFile($dto->avatar, 'avatars');
        }
        $data = [

            'name' => $dto->name,

            'email' => $dto->email,

            'password' => $dto->password,

            'phone' => $dto->phone,

            'status' => $dto->status ?? true,
            'avatar' => $avatar,

        ];

        $user = $this->users->create($data);
        $user->assignRole($role);
        return $user->fresh();
    }
}
