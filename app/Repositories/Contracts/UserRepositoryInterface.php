<?php

namespace App\Repositories\Contracts;

use App\DTOs\User\UserFilterData;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function findByName(string $name): ?User;
    public function search(
        UserFilterData $filters
    ): LengthAwarePaginator;

    public function softDelete(int $id): bool;

    /**
     * استرجاع حساب المستخدم المحذوف منطقياً.
     */
    public function restore(int $id): bool;

    /**
     * الحذف النهائي للمستخدم من قاعدة البيانات (لا يمكن التراجع عنه).
     */
    public function forceDelete(int $id): bool;

    /**
     * جلب قائمة بالمستخدمين المحذوفين منطقياً فقط (سلة المهملات).
     */
    public function getOnlyTrashed(): Collection;

    /**
     * جلب جميع المستخدمين (النشطين بالإضافة إلى المحذوفين منطقياً).
     */
    public function getAllWithTrashed(): Collection;

    /**
     * التحقق مما إذا كان المستخدم محذوفاً منطقياً أم لا.
     */
    public function isTrashed(int $id): bool;
}
