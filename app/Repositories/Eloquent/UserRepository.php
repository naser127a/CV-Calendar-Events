<?php

namespace App\Repositories\Eloquent;

use App\DTOs\User\UserFilterData;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        $this->model = $user;
    }
    public function findByEmail(string $email): ?User
    {
        return  $this->model->where('email', $email)->first();
    }

    public function findByName(string $name): ?User
    {
        return  $this->model
            ->where('name', $name)->first();
    }

    public function search(
        UserFilterData $filters
    ): LengthAwarePaginator {
        $query = User::query()
            ->with('roles');

        if ($filters->search) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters->search}%")
                    ->orWhere('email', 'like', "%{$filters->search}%")
                    ->orWhere('phone', 'like', "%{$filters->search}%");
            });
        }

        if (! is_null($filters->status)) {
            $query->where(
                'status',
                $filters->status
            );
        }

        if ($filters->roleName) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where(
                    'name',
                    $filters->roleName
                );
            });
        }

        return $query
            ->orderBy(
                $filters->sort_by,
                $filters->sort_dir
            )
            ->paginate($filters->per_page);
    }

    // 1. الحذف المنطقي
    public function softDelete(int $id): bool
    {
        $user = $this->model->findOrFail($id);
        return $user->delete();
    }

    // 2. استرجاع المستخدم
    public function restore(int $id): bool
    {
        $user = $this->model->withTrashed()->findOrFail($id);
        return $user->restore();
    }

    // 3. الحذف النهائي
    public function forceDelete(int $id): bool
    {
        $user = $this->model->withTrashed()->findOrFail($id);
        return $user->forceDelete();
    }

    // 4. جلب الكل (النشط والمحذوف)
    public function getAllWithTrashed(): Collection
    {
        return $this->model->withTrashed()->get();
    }

    // 5. جلب المحذوفين فقط
    public function getOnlyTrashed(): Collection
    {
        return $this->model->onlyTrashed()->get();
    }
    public function isTrashed(int $id): bool
    {
        $user = $this->model->withTrashed()->findOrFail($id);
        return $user->trashed(); // هذه الدالة في لارافيل ترجع true إذا كان محذوفاً
    }
}
