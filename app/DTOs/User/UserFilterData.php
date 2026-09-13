<?php

namespace App\DTOs\User;

use App\Http\Requests\User\UserFilterRequest;

class UserFilterData
{
    public function __construct(

        public readonly ?string $search,

        public readonly ?bool $status,

        public readonly ?string $roleName,

        public readonly string $sort_by,

        public readonly string $sort_dir,

        public readonly int $per_page,

    ) {}

    public static function fromRequest(UserFilterRequest $request): self
    {
        return new self(

            search: $request->search,

            status: $request->status,

            roleName: $request->roleName,

            sort_by: $request->sort_by ?? 'id',

            sort_dir: $request->sort_dir ?? 'desc',

            per_page: $request->per_page ?? 15,

        );
    }
}
