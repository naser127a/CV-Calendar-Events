<?php

namespace App\DTOs\Calendar;

use App\Http\Requests\Calendar\CalendarFilterRequest;

class CalendarFilterData
{
    public function __construct(
        public readonly ?string $search,
        public readonly ?bool $status,
        public readonly ?string $color,
        public readonly string $sort_by,
        public readonly string $sort_dir,
        public readonly int $per_page,
    ) {}

    public static function fromRequest(CalendarFilterRequest $request): self
    {
        return new self(
            search: $request->search,
            status: $request->status,
            color: $request->color,
            sort_by: $request->sort_by ?? 'id',
            sort_dir: $request->sort_dir ?? 'desc',
            per_page: $request->per_page ?? 15,
        );
    }
}
