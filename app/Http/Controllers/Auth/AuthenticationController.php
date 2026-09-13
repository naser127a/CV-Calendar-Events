<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\LoginData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use App\Http\Resources\UserResource;

class AuthenticationController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}
    //
    public function login(LoginRequest $request)
    {
        $dto = LoginData::fromRequest($request);
        $result = $this->authService
            ->login($dto);
        return ApiResponse::success([
            'user'  => new UserResource($result['user']),
            'token' => $result['token'],
        ], 'Login Successful');
    }

    public function logout(Request $request)
    {
        $this->authService
            ->logout($request->user());

        return ApiResponse::success(
            null,
            'Logout Successful'
        );
    }

    public function logoutAll(Request $request)
    {
        $this->authService->logoutAll($request->user());
        return ApiResponse::success(
            null,
            'Logout from all devices Successful'
        );
    }

    public function me(Request $request)
    {
        return ApiResponse::success(
            new UserResource($this->authService->me($request->user())),
            'User Profile'
        );
    }
}
