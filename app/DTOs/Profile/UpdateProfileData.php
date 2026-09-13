<?php

namespace App\DTOs\Profile;

use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\UploadedFile;

class UpdateProfileData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $last_name,
        public readonly ?string $email,
        public readonly ?string $phone,
        // public readonly ?string $address,
        public readonly ?UploadedFile $avatar
    ) {
        //
    }

    public static function fromRequest(UpdateProfileRequest $request): self
    {
        return new self(
            name: $request->input('name'),
            last_name: $request->input('last_name'),
            email: $request->input('email'),
            phone: $request->input('phone'),
            // address: $request->input('address') ?? null,
            avatar: $request->file('avatar')
        );
    }
}
