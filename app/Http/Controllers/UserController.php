<?php

namespace App\Http\Controllers;

use App\DTOs\User\CreateUserData;
use App\DTOs\User\UpdateUserData;
use App\DTOs\User\UserFilterData;
use App\Helpers\ApiResponse;
use App\Http\Requests\User\ChangeRoleRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UserFilterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\UserService;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * عرض قائمة المستخدمين مع الترقيم (Pagination)
     */
    public function index(UserFilterRequest $request)
    {
        $this->authorize('viewAny', User::class);
        $filters = UserFilterData::fromRequest($request);

        $users = $this->userService
            ->search($filters);

        return ApiResponse::paginated(

            UserResource::collection($users),

            'Users retrieved successfully.'

        );
    }

    /**
     * إنشاء مستخدم جديد
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        $dto = CreateUserData::fromRequest($request);
        $user = $this->userService->create($dto);

        return ApiResponse::success(
            new UserResource($user),
            'تم إنشاء المستخدم بنجاح',
            Response::HTTP_CREATED
        );
    }

    /**
     * عرض تفاصيل مستخدم معين
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        return ApiResponse::success(
            new UserResource($user),
            'تم جلب بيانات المستخدم بنجاح'
        );
    }

    /**
     * تحديث بيانات مستخدم
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $dto = UpdateUserData::fromRequest($request);
        $updatedUser = $this->userService->update($user, $dto);

        return ApiResponse::success(
            new UserResource($updatedUser),
            'تم تحديث بيانات المستخدم بنجاح'
        );
    }


    /**
     * حذف مستخدم
     */
    public function destroy(User $user)
    {

        $this->authorize('delete', $user);
        $this->userService->delete($user);

        return ApiResponse::success(
            null,
            'تم حذف المستخدم بنجاح'
        );
    }

    /**
     * تفعيل حساب المستخدم
     */
    public function activate(User $user)
    {
        $this->authorize('activate', $user);
        $this->userService->activate($user);

        return ApiResponse::success(
            null,
            'تم تفعيل حساب المستخدم بنجاح'
        );
    }

    /**
     * إلغاء تفعيل حساب المستخدم
     */
    public function deactivate(User $user)
    {
        $this->authorize('deactivate', $user);
        $this->userService->deactivate($user);

        return ApiResponse::success(
            null,
            'تم إلغاء تفعيل حساب المستخدم بنجاح'
        );
    }

    /**
     * تغيير دور المستخدم (Role)
     */
    public function changeRole(ChangeRoleRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $this->userService->changeRole($user, $request->validated('roleName'));

        return ApiResponse::success(
            null,
            'تم تغيير دور المستخدم بنجاح'
        );
    }
    /**
     * جلب قائمة بالمستخدمين المحذوفين منطقياً (سلة المهملات)
     */
    public function trashed()
    {
        $this->authorize('viewTrashed', User::class);

        $trashedUsers = $this->userService->getTrashedUsersOnly();

        return ApiResponse::success(
            $trashedUsers,
            'تم جلب قائمة المستخدمين المحذوفين بنجاح'
        );
    }

    /**
     * الحذف المنطقي للمستخدم
     */
    public function softDelete(int $id)
    {
        $user = User::query()->findOrFail($id);
        $this->authorize('delete', $user);
        $this->userService->softDeleteUser($id);

        return ApiResponse::success(
            null,
            'تم نقل المستخدم إلى سلة المهملات بنجاح'
        );
    }

    /**
     * استرجاع المستخدم المحذوف منطقياً
     */
    public function restore(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $user);
        $this->userService->restoreUser($id);

        return ApiResponse::success(
            null,
            'تم استرجاع حساب المستخدم بنجاح'
        );
    }

    /**
     * الحذف النهائي للمستخدم (Hard Delete)
     */
    public function forceDelete(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $user);
        $this->userService->forceDeleteUser($id);

        return ApiResponse::success(
            null,
            'تم حذف المستخدم من النظام نهائياً'
        );
    }
}
