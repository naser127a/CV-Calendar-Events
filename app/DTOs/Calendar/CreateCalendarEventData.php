<?php

namespace App\DTOs\Calendar;

use App\Http\Requests\Calendar\StoreCalendarRequest;

class CreateCalendarEventData
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $color,
        public readonly ?string $end_at,
        public readonly string $start_at,
        public readonly ?bool $status,
        public readonly ?bool $all_day,
    ) {}

    public static function fromRequest(StoreCalendarRequest $request): self
    {
        return new self(
            title: $request->input('title'),
            description: $request->input('description'),
            color: $request->input('color'),
            end_at: $request->input('end_at'),
            start_at: $request->input('start_at'),
            status: $request->boolean('status'),
            all_day: $request->boolean('all_day'),
        );
    }
}
