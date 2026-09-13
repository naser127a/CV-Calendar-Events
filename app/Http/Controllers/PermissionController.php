<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Permission\CreatePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(
        private PermissionService $permissionService
    ) {}
    public function index(Request $request)
    {
        $this->authorize('viewAny', Permission::class);

        if ($request->filled('name')) {
            $permission = $this->permissionService
                ->findByName($request->name);

            return ApiResponse::success(
                new PermissionResource($permission),
                'Permission retrieved successfully.'
            );
        }

        return ApiResponse::paginated(
            PermissionResource::collection(
                $this->permissionService->paginate(
                    (int) $request->input('per_page', 15)
                )
            ),
            " Permissions retrieved successfully"
        );
    }

    public function show(Permission $permission)
    {
        $this->authorize('view', $permission);

        return ApiResponse::success(
            new PermissionResource($permission)
        );
    }
    public function store(CreatePermissionRequest $request)
    {
        $this->authorize('create', Permission::class);

        $permission = $this->permissionService->create(
            $request->validated()
        );

        return ApiResponse::success(
            new PermissionResource($permission),
            "Permission created successfully.",
            201
        );
    }

    public function update(
        UpdatePermissionRequest $request,
        Permission $permission
    ) {
        $this->authorize('update', $permission);

        $updatedPermission = $this->permissionService
            ->update(
                $permission,
                $request->validated()
            );

        return ApiResponse::success(
            new PermissionResource($updatedPermission),
            "Permission updated successfully.",
            200
        );
    }

    public function destroy(Permission $permission)
    {
        $this->authorize('delete', $permission);

        $this->permissionService->delete($permission);

        return ApiResponse::success(
            null,
            'Permission deleted successfully'
        );
    }
}
