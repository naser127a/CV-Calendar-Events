<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Requests\User\SyncRolePermissionsRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        private RoleService $roleService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Role::class);

        if ($request->filled('name')) {
            $role = $this->roleService->findByName(
                $request->input('name')
            );

            return ApiResponse::success(
                new RoleResource($role),
                'Role retrieved successfully.'
            );
        }

        return ApiResponse::paginated(
            RoleResource::collection(
                $this->roleService->paginate(
                    (int) $request->input('per_page', 15)
                )
            ),
            'Roles retrieved successfully.'
        );
    }

    public function show(Role $role)
    {
        $this->authorize('view', $role);

        return ApiResponse::success(
            new RoleResource($role),
            'Role retrieved successfully.'
        );
    }

    public function store(CreateRoleRequest $request)
    {
        $this->authorize('create', Role::class);

        $role = $this->roleService->create(
            $request->validated()
        );

        return ApiResponse::success(
            new RoleResource($role),
            'Role created successfully.',
            201
        );
    }

    public function update(
        UpdateRoleRequest $request,
        Role $role
    ) {
        $this->authorize('update', $role);

        $updatedRole = $this->roleService->update(
            $role,
            $request->validated()
        );

        return ApiResponse::success(
            new RoleResource($updatedRole),
            'Role updated successfully.'
        );
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $this->roleService->delete($role);

        return ApiResponse::success(
            null,
            'Role deleted successfully.'
        );
    }

    public function permissions(Role $role, Request $request)
    {
        $this->authorize('view', $role);

        $permissions = $this->roleService->getPermissions(
            $role,
            (int) $request->input('per_page', 15)
        );

        return ApiResponse::paginated(
            PermissionResource::collection($permissions),
            'Permissions retrieved successfully.'
        );
    }

    public function syncPermissions(
        SyncRolePermissionsRequest $request,
        Role $role
    ) {
        $this->authorize('update', $role);

        $this->roleService->syncPermissions(
            $role,
            $request->validated('permissions')
        );

        return ApiResponse::success(
            null,
            'Permissions synchronized successfully.'
        );
    }
}
