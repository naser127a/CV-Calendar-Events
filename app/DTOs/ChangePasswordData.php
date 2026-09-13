<?php

namespace App\DTOs;

use App\Http\Requests\Auth\ChangePasswordRequest;

class ChangePasswordData
{
    public function __construct(
        public string $currentPassword,
        public string $newPassword,
    ) {}
    public static function fromRequest(ChangePasswordRequest $request): self
    {
        return new self(
            currentPassword: $request->input('current_password'),
            newPassword: $request->input('password')
        );
    }
}
