<?php

namespace App\DTOs\User;

use App\Http\Requests\User\StoreUserRequest;
use Illuminate\Http\UploadedFile;

class CreateUserData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $phone = null,
        public readonly ?UploadedFile $avatar = null,
        public readonly ?string $roleName,
        public readonly ?bool $status

    ) {}
    public static function fromRequest(StoreUserRequest $request): self
    {
        return new self(
            name: $request->name,
            email: $request->email,
            password: $request->password,
            phone: $request->phone,
            avatar: $request->file('avatar'),
            status: $request->status,
            roleName: $request->roleName
        );
    }
}
