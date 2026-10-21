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
            'display_name' => $data['display_name'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update(
        Permission $permission,
        array $data
    ): Permission {
        $permission->fill(array_intersect_key(
            $data,
            array_flip(['name', 'display_name', 'description', 'is_active'])
        ))->save();

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
