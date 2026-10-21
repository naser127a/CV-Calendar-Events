<?php

namespace App\Services;

use App\Actions\User\ActivateUserAction;
use App\Actions\User\ChangeUserRoleAction;
use App\Actions\User\CreateUserAction;
use App\Actions\User\DeactivateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Actions\User\UpdateUserAction;
use App\DTOs\User\CreateUserData;
use App\DTOs\User\UpdateUserData;
use App\DTOs\User\UserFilterData;
use App\Events\User\UserRoleChanged;
use App\Events\User\UserUpdated;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService extends BaseService
{
    public function __construct(
        private UserRepositoryInterface $users,

        private CreateUserAction $createUserAction,
        private UpdateUserAction $updateUserAction,
        private DeleteUserAction $deleteUserAction,
        private ActivateUserAction $activateUserAction,
        private DeactivateUserAction $deactivateUserAction,
        private ChangeUserRoleAction $changeUserRoleAction,

    ) {
        parent::__construct($users);
    }

    public function create(CreateUserData $data): User
    {
        return DB::transaction(
            fn() => $this->createUserAction->execute($data)
        );
    }
    private function sanitizeChanges(array $changes): array
    {
        unset(
            $changes['password'],
            $changes['remember_token']
        );

        return $changes;
    }
    public function update(User $user, UpdateUserData $data): User
    {
        return DB::transaction(function () use ($user, $data) {

            $old = $user->getOriginal();

            $updatedUser = $this->updateUserAction->execute(
                $user,
                $data
            );

            $changes = $updatedUser->getChanges();
            $changes = $this->sanitizeChanges($changes);

            UserUpdated::dispatch(
                user: $updatedUser,
                old: $old,
                changes: $changes,
                updatedBy: Auth::id(),
            );

            return $updatedUser;
        });
    }

    public function delete(User $user): bool
    {
        return DB::transaction(
            fn() => $this->deleteUserAction->execute($user)
        );
    }

    public function activate(User $user): User
    {
        return DB::transaction(
            fn() => $this->activateUserAction->execute($user)
        );
    }

    public function deactivate(User $user): User
    {
        return DB::transaction(
            fn() => $this->deactivateUserAction->execute($user)
        );
    }

    public function changeRole(
        User $user,
        string $roleName
    ): User {
        return DB::transaction(function () use ($user, $roleName) {

            $oldRoles = $user->getRoleNames()->all();
            $changedBy = Auth::id();
            try {
                $updatedUser = $this->changeUserRoleAction->execute(
                    $user,
                    $roleName
                );
                $newRoles = $updatedUser->getRoleNames()->all();
                activity()
                    ->performedOn($user)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old_roles' => $oldRoles,
                        'new_roles' => $newRoles,
                    ])
                    ->log('role_changed');
                UserRoleChanged::dispatch(
                    user: $updatedUser,
                    oldRoles: $oldRoles,
                    newRoles: $newRoles,
                    changedBy: $changedBy,
                );
            } catch (\Throwable $e) {
                activity()
                    ->performedOn($user)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old_roles' => $oldRoles,
                        'new_roles' => "error" . $e->getMessage(),
                    ])
                    ->log('role_change_failed');

                throw $e;
            }


            return $updatedUser;
        });
    }

    public function search(
        UserFilterData $filters
    ): LengthAwarePaginator {
        return $this->users->search($filters);
    }

    /**
     * الحذف المنطقي للمستخدم باستخدام المعاملات (Transactions)
     */
    public function softDeleteUser(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = $this->users->findById($id);
            $deleted = $this->deleteUserAction->execute($user);

            Log::info("تم الحذف المنطقي للمستخدم رقم: {$id} بواسطة النظام.");

            return $deleted;
        });
    }

    /**
     * استرجاع المستخدم المحذوف
     */
    public function restoreUser(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $restored = $this->users->restore($id);
            Log::info("تم استرجاع المستخدم رقم: {$id}");

            return $restored;
        });
    }

    /**
     * الحذف النهائي للمستخدم (Hard Delete)
     */
    public function forceDeleteUser(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = User::withTrashed()->findOrFail($id);
            if (Auth::id() === $user->id) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'user' => 'لا يمكنك حذف حسابك.',
                ]);
            }

            if (
                $user->hasRole('admin')
                && User::role('admin')->count() <= 1
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'user' => 'لا يمكن حذف آخر مدير في النظام.',
                ]);
            }

            $deleted = $this->users->forceDelete($id);
            Log::warning("تنبيه: تم الحذف النهائي للمستخدم رقم: {$id} ولا يمكن التراجع عن هذه العملية.");

            return $deleted;
        });
    }

    /**
     * جلب جميع المستخدمين (النشطين والمحذوفين)
     */
    public function getAllUsersIncludingTrashed(): Collection
    {
        return $this->users->getAllWithTrashed();
    }

    /**
     * جلب المستخدمين المحذوفين فقط (سلة المهملات)
     */
    public function getTrashedUsersOnly(): Collection
    {
        return $this->users->getOnlyTrashed();
    }

    /**
     * فحص ما إذا كان المستخدم محذوفاً منطقياً
     */
    public function isUserTrashed(int $id): bool
    {
        return $this->users->isTrashed($id);
    }
}
