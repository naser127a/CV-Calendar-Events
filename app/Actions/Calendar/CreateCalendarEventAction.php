<?php

namespace App\Actions\Calendar;

use App\DTOs\Calendar\CreateCalendarEventData;
use App\Models\CalendarEvent;
use App\Repositories\Contracts\CalendarEventRepositoryInterface;

class CreateCalendarEventAction
{
    public function __construct(
        private CalendarEventRepositoryInterface $calendars
    ) {}

    public function execute(CreateCalendarEventData $dto, int $userId): CalendarEvent
    {
        return $this->calendars->create([
            'title' => $dto->title,
            'description' => $dto->description,
            'color' => $dto->color,
            'end_at' => $dto->end_at,
            'start_at' => $dto->start_at,
            'status' => $dto->status ?? true,
            'all_day' => $dto->all_day ?? false,
            'created_by' => $userId,
        ]);
    }
}
