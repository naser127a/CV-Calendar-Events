<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\DTOs\RegisterData;
use App\Http\Resources\UserResource;

class RegistrationController extends Controller
{
    //
    public function __construct(
        private AuthService $authService
    ) {}

    public function register(RegisterRequest $request)
    {

        $dto = RegisterData::fromRequest($request);
        $result = $this->authService
            ->register($dto);

        return ApiResponse::success(
            [
                'user'  => new UserResource($result['user']),
                'token' => $result['token'],
            ],
            'User Registered Successfully'
        );
    }
}
