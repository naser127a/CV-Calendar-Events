<?php

namespace App\Http\Controllers;

use App\DTOs\Profile\UpdateProfileData;
use App\Helpers\ApiResponse;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileService;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profile
    ) {}
    public function me()
    {
        $user = Auth::user();
        return ApiResponse::success(
            new ProfileResource(
                $this->profile->me($user)
            ),
            "Profile retrieved successfully."
        );
    }

    public function update(UpdateProfileRequest $request)
    {
        // $this->authorize('update', Auth::user());
        $user = Auth::user();
        $data = UpdateProfileData::fromRequest($request);
        $updatedUser = $this->profile->update($data, $user);

        return ApiResponse::success(
            new ProfileResource($updatedUser),
            "Profile updated successfully."
        );
    }
}
