<?php

namespace App\Actions\Auth;

use App\DTOs\ChangePasswordData;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class ChangePasswordAction
{
    public function __construct(
        private UserRepositoryInterface $users
    ) {}
    public function execute(User $user, ChangePasswordData $dto)
    {
        if (!Hash::check($dto->currentPassword, $user->password)) {
            throw ValidationException::withMessages(
                ["current_password" => "كلمة السر الحالية غير صحيحة!"]
            );
        }


        $this->users->update(
            $user,
            [
                'password' => Hash::make($dto->newPassword)
            ]
        );
    }
}
