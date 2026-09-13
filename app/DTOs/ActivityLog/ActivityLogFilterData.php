<?php

namespace App\DTOs\ActivityLog;

use App\Http\Requests\ActivityLog\ActivityLogFilterRequest;

class ActivityLogFilterData
{
    public function __construct(
        public readonly ?string $search,
        public readonly ?int $causer_id,
        public readonly ?string $event,
        public readonly ?string $subject_type,
        public readonly ?int $subject_id,
        public readonly ?string $date_from,
        public readonly ?string $date_to,
        public readonly string $sort_by,
        public readonly string $sort_dir,
        public readonly int $per_page,
    ) {}

    public static function fromRequest(
        ActivityLogFilterRequest $request
    ): self {
        return new self(
            search: $request->input('search'),

            causer_id: $request->integer('causer_id') ?: null,

            event: $request->input('event'),

            subject_type: $request->input('subject_type'),

            subject_id: $request->integer('subject_id') ?: null,

            date_from: $request->input('date_from'),

            date_to: $request->input('date_to'),

            sort_by: $request->input(
                'sort_by',
                'created_at'
            ),

            sort_dir: $request->input(
                'sort_dir',
                'desc'
            ),

            per_page: $request->integer(
                'per_page',
                15
            ),
        );
    }
}
