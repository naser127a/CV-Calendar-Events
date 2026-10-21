<?php

namespace App\Services;

use App\Events\Role\RoleChanged;
use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function getAllRoles(): Collection
    {
        return Cache::remember(
            'roles.all',
            now()->addHour(),
            fn () => Role::query()
                ->orderBy('name')
                ->with('permissions')
                ->get()
        );
    }

    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {

            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web',
                'display_name' => $data['display_name'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (! empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }
            RoleChanged::dispatch(
                role: $role->fresh(),
                action: 'created',
            );

            return $role->load('permissions');
        });
    }

    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {

            $role->fill(array_intersect_key(
                $data,
                array_flip(['name', 'display_name', 'description', 'is_active'])
            ))->save();

            if (array_key_exists('permissions', $data)) {
                $role->syncPermissions(
                    $data['permissions']
                );
            }
            RoleChanged::dispatch(
                role: $role->fresh(),
                action: 'updated',
            );

            return $role->fresh()->load('permissions');
        });
    }

    public function delete(Role $role): bool
    {
        RoleChanged::dispatch(
            role: $role->fresh(),
            action: 'deleted',
        );

        return DB::transaction(
            fn () => (bool) $role->delete()
        );
    }

    public function attachPermissions(
        Role $role,
        array $permissions
    ): Role {
        return DB::transaction(function () use ($role, $permissions) {

            $role->givePermissionTo($permissions);

            return $role->fresh()->load('permissions');
        });
    }

    public function detachPermissions(
        Role $role,
        array $permissions
    ): Role {
        return DB::transaction(function () use ($role, $permissions) {
            foreach ($permissions as $permission) {
                $role->revokePermissionTo($permission);
            }

            return $role->fresh()->load('permissions');
        });
    }

    public function getPermissions(
        Role $role,
        int $perPage = 15
    ): LengthAwarePaginator {
        return $role->permissions()
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function syncPermissions(
        Role $role,
        array $permissions
    ): Role {
        return DB::transaction(function () use ($role, $permissions) {

            $role->syncPermissions($permissions);
            RoleChanged::dispatch(
                role: $role->fresh(),
                action: 'Sync',
            );

            return $role->fresh()->load('permissions');
        });
    }

    public function findByName(string $name, string $guard = 'web'): Role
    {
        return Role::findByName($name, $guard)->load('permissions');
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Role::with('permissions')
            ->orderBy('name')
            ->paginate($perPage);
    }
}
