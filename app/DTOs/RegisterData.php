<?php

namespace App\DTOs;

use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\UploadedFile;

class RegisterData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $phone = null,
        public readonly ?UploadedFile $avatar = null
    ) {}
    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            name: $request->name,
            email: $request->email,
            password: $request->password,
            phone: $request->phone,
            avatar: $request->file('avatar')
        );
    }
}
