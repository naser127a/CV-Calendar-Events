<?php

namespace App\DTOs\User;

use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Http\UploadedFile;

class UpdateUserData
{
    public function __construct(
        public ?string $name = NULL,
        public ?string $email = null,
        public ?string $phone = null,
        public ?UploadedFile $avatar = null
    ) {}
    public static function fromRequest(UpdateUserRequest $request): self
    {
        return new self(
            name: $request->name,
            email: $request->email,
            phone: $request->phone,
            avatar: $request->file('avatar'),
        );
    }
}
