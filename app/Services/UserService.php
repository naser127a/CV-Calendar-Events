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
        try {
            return DB::transaction(function () use ($id) {
                $deleted = $this->users->softDelete($id);

                // يمكنك هنا إضافة حذف منطقي للبيانات المرتبطة (مثل مقالات المستخدم)
                // $this->postRepository->softDeleteByUser($id);

                Log::info("تم الحذف المنطقي للمستخدم رقم: {$id} بواسطة النظام.");

                return $deleted;
            });
        } catch (Exception $e) {
            Log::error("خطأ أثناء الحذف المنطقي للمستخدم {$id}: " . $e->getMessage());
            throw new Exception("تعذر حذف المستخدم في الوقت الحالي.");
        }
    }

    /**
     * استرجاع المستخدم المحذوف
     */
    public function restoreUser(int $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $restored = $this->users->restore($id);

                Log::info("تم استرجاع المستخدم رقم: {$id}");

                return $restored;
            });
        } catch (Exception $e) {
            Log::error("خطأ أثناء استرجاع المستخدم {$id}: " . $e->getMessage());
            throw new Exception("تعذر استرجاع المستخدم.");
        }
    }

    /**
     * الحذف النهائي للمستخدم (Hard Delete)
     */
    public function forceDeleteUser(int $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                // ملاحظة: هنا المكان الأنسب لحذف الملفات المرفوعة للمستخدم من الـ Storage
                // مثال: Storage::delete('users/avatars/' . $id . '.jpg');

                $deleted = $this->users->forceDelete($id);

                Log::warning("تنبيه: تم الحذف النهائي للمستخدم رقم: {$id} ولا يمكن التراجع عن هذه العملية.");

                return $deleted;
            });
        } catch (Exception $e) {
            Log::critical("فشل الحذف النهائي للمستخدم {$id}: " . $e->getMessage());
            throw new Exception("تعذر حذف المستخدم نهائياً.");
        }
    }

    /**
     * جلب جميع المستخدمين (النشطين والمحذوفين)
     */
    public function getAllUsersIncludingTrashed(): Collection
    {
        try {
            return $this->users->getAllWithTrashed();
        } catch (Exception $e) {
            Log::error("خطأ في جلب كل المستخدمين: " . $e->getMessage());
            return new Collection(); // إرجاع مجموعة فارغة في حالة الخطأ لتجنب توقف النظام
        }
    }

    /**
     * جلب المستخدمين المحذوفين فقط (سلة المهملات)
     */
    public function getTrashedUsersOnly(): Collection
    {
        try {
            return $this->users->getOnlyTrashed();
        } catch (Exception $e) {
            Log::error("خطأ في جلب المستخدمين المحذوفين: " . $e->getMessage());
            return new Collection();
        }
    }

    /**
     * فحص ما إذا كان المستخدم محذوفاً منطقياً
     */
    public function isUserTrashed(int $id): bool
    {
        try {
            return $this->users->isTrashed($id);
        } catch (Exception $e) {
            return false; // إذا لم يتم العثور على المستخدم، نعتبره غير محذوف أو نعالج الخطأ
        }
    }
}
