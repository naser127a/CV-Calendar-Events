<?php

namespace App\Actions\Calendar;

use App\DTOs\Calendar\UpdateCalendarEventData;
use App\Models\CalendarEvent;
use App\Repositories\Contracts\CalendarEventRepositoryInterface;

class UpdateCalendarEventAction
{
    public function __construct(
        private CalendarEventRepositoryInterface $calendars
    ) {}

    public function execute(CalendarEvent $calendar, UpdateCalendarEventData $dto): CalendarEvent
    {

        $data = array_filter([
            'title' => $dto->title,
            'description' => $dto->description,
            'color' => $dto->color,
            'end_at' => $dto->end_at,
            'start_at' => $dto->start_at,
            'status' => $dto->status,
            'all_day' => $dto->all_day,
        ], fn ($value) => $value !== null);

        return $this->calendars->update($calendar, $data);
    }
}
