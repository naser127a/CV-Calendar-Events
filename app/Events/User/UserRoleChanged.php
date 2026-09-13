<?php

namespace App\Events\User;

use App\Models\User;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRoleChanged implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly array $oldRoles,
        public readonly array $newRoles,
        public readonly int $changedBy,
    ) {}
}
