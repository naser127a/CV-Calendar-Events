<?php

namespace App\Actions\Profile;

use App\DTOs\Profile\UpdateProfileData;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Traits\HandlesFileUpload;

class UpdateProfileAction
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

    public function execute(UpdateProfileData $data, User $user): User
    {


        if ($data->avatar && $user->avatar) {
            $avatar = $this->replaceFile($user->avatar, $data->avatar, 'avatars');
        } else if ($data->avatar) {
            $avatar = $this->uploadFile($data->avatar, 'avatars');
        }
        $avatar = null;
        return $this->users->update($user, $this->filter_array([
            'name' => $data->name,
            'last_name' => $data->last_name,
            'email' => $data->email,
            'phone' => $data->phone,
            // 'address' => $data->address,
            'avatar' => $avatar
        ]));
    }

    public function filter_array(array $data): array
    {
        return array_filter($data, function ($value) {
            return !is_null($value);
        });
    }
}
