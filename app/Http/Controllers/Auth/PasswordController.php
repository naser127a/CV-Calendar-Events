<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ChangePasswordAction;
use App\DTOs\ChangePasswordData;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}
    public function changePassword(ChangePasswordRequest $request)
    {
        $dto = ChangePasswordData::fromRequest($request);
        $user = Auth::user();
        $this->authService->changePassword($user, $dto);

        return ApiResponse::success(
            null,
            "Password changed successfully."
        );
    }
}
