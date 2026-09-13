<?php

namespace App\Actions\User;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DeleteUserAction
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UserRepositoryInterface $users
    ) {
        //
    }
    public function execute(User $user): bool
    {
        if (Auth::id() === $user->id)
            {
                throw ValidationException::withMessages([
                    'user' => 'لا يمكنك حذف حسابك.'
                ]);
            }
        if ($user->hasRole('admin')) {

            $admins = User::whereHas('role', function ($query) {
                $query->where('name', 'admin');
            })->count();

            if ($admins <= 1) {
                throw ValidationException::withMessages([
                    'user' => 'لا يمكن حذف آخر مدير في النظام.'
                ]);
            }
        }

        return $this->users->delete($user);
    }
}
