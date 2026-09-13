<?php

namespace App\Actions\Calendar;

use App\Models\CalendarEvent;
use App\Repositories\Contracts\CalendarEventRepositoryInterface;

class DeleteCalendarEventAction
{
   public function __construct(
        private CalendarEventRepositoryInterface $calendars
    ) {
        //
    }
    public function execute(CalendarEvent $calendar): bool
    {
        return $this->calendars->delete($calendar);
    }
}
