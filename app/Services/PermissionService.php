<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PermissionService
{
    public function create(array $data): Permission
    {
        return Permission::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);
    }

    public function update(
        Permission $permission,
        array $data
    ): Permission {
        $permission->update([
            'name' => $data['name'] ?? $permission->name,
        ]);

        return $permission->fresh();
    }

    public function delete(Permission $permission): bool
    {
        return (bool) $permission->delete();
    }

    public function findByName(string $name): Permission
    {
        return Permission::query()
            ->where('name', $name)
            ->firstOrFail();
    }

    public function getAllPermissions(): Collection
    {
        return Permission::query()->get();
    }

    public function paginate(
        int $perPage = 15
    ): LengthAwarePaginator {
        return Permission::query()
            ->paginate($perPage);
    }
}
