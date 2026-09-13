<?php

namespace App\DTOs\Calendar;

use App\Http\Requests\Calendar\UpdateCalendarRequest;

class UpdateCalendarEventData
{
    public function __construct(
        public readonly ?string $title,
        public readonly ?string $description = null,
        public readonly ?string $color = null,
        public readonly ?string $end_at = null,
        public readonly ?string $start_at = null,
        public readonly ?bool $status = null,
        public readonly ?bool $all_day = null,
    ) {}

    public static function fromRequest(UpdateCalendarRequest $request): self
    {
        return new self(
            title: $request->input('title'),
            description: $request->input('description'),
            color: $request->input('color'),
            end_at: $request->input('end_at'),
            start_at: $request->input('start_at'),
            status: $request->has('status') ? $request->boolean('status') : null,
            all_day: $request->has('all_day') ? $request->boolean('all_day') : null,
        );
    }
}
