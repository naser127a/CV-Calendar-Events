<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ChangeUserRoleAction
{
    public function execute(
        User $user,
        string $roleName
    ): User {
        if (Auth::id() === $user->id) {
            throw ValidationException::withMessages([
                'user' => 'لا يمكنك تغيير دور حسابك.',
            ]);
        }

        if ($user->hasRole($roleName)) {
            throw ValidationException::withMessages([
                'role' => 'User already has this role.',
            ]);
        }

        $user->syncRoles([$roleName]);

        return $user->fresh();
    }
}



// class ChangeUserRoleAction
// {
//     public function execute(
//         User $user,
//         array $rolesName
//     ): User {
//         if (Auth::id() === $user->id) {
//             throw ValidationException::withMessages([
//                 'user' => 'لا يمكنك تغيير أدوار حسابك.',
//             ]);
//         }

//         if (empty($rolesName)) {
//             throw ValidationException::withMessages([
//                 'roles' => 'يجب تحديد دور واحد على الأقل.',
//             ]);
//         }

//         $currentRoles = $user->getRoleNames()->all();

//         if ($currentRoles === $rolesName) {
//             throw ValidationException::withMessages([
//                 'roles' => 'المستخدم يملك هذه الأدوار بالفعل.',
//             ]);
//         }

//         $user->syncRoles($rolesName);

//         return $user->fresh();
//     }
// } -->
