<?php

namespace App\Events\User;

use App\Models\User;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUpdated implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly array $old,
        public readonly array $changes,
        public readonly int $updatedBy,
    ) {}
}
