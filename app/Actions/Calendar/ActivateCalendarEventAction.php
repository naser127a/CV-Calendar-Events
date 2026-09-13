<?php

namespace App\Actions\Calendar;

use App\Models\CalendarEvent;
use App\Repositories\Contracts\CalendarEventRepositoryInterface;

class ActivateCalendarEventAction
{
    public function __construct(
        private CalendarEventRepositoryInterface $calendars
    ) {
        //
    }
    public function execute(CalendarEvent $calendar): CalendarEvent
    {
        return $this->calendars->update($calendar, [
            'status'=>true
        ]);
    }
}
