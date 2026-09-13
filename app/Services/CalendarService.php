<?php

namespace App\Services;

use App\Actions\Calendar\ActivateCalendarEventAction;
use App\Actions\Calendar\CreateCalendarEventAction;
use App\Actions\Calendar\DeactivateCalendarEventAction;
use App\Actions\Calendar\DeleteCalendarEventAction;
use App\Actions\Calendar\UpdateCalendarEventAction;
use App\DTOs\Calendar\CalendarFilterData;
use App\DTOs\Calendar\CreateCalendarEventData;
use App\DTOs\Calendar\UpdateCalendarEventData;
use App\Models\CalendarEvent;
use App\Models\User;
use App\Repositories\Contracts\CalendarEventRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CalendarService extends BaseService
{
    public function __construct(
        private CalendarEventRepositoryInterface $calendars,
        private ActivateCalendarEventAction $activateCalendarEventAction,
        private DeactivateCalendarEventAction $deactivateCalendarEventAction,
        private CreateCalendarEventAction $createCalendarEventAction,
        private UpdateCalendarEventAction $updateCalendarEventAction,
        private DeleteCalendarEventAction $deleteCalendarEventAction
    ) {
        parent::__construct($calendars);
    }

    public function create(CreateCalendarEventData $dto, int $userId): CalendarEvent
    {
        return DB::transaction(function () use ($dto, $userId) {
            return $this->createCalendarEventAction->execute($dto, $userId);
        });
    }

    public function update(CalendarEvent $calendar, UpdateCalendarEventData $dto): CalendarEvent
    {
        return DB::transaction(function () use ($calendar, $dto) {
            return $this->updateCalendarEventAction->execute($calendar, $dto);
        });
    }

    public function activate(CalendarEvent $calendar): CalendarEvent
    {
        return DB::transaction(function () use ($calendar) {
            return $this->activateCalendarEventAction->execute($calendar);
        });
    }

    public function deactivate(CalendarEvent $calendar): CalendarEvent
    {
        return DB::transaction(function () use ($calendar) {
            return $this->deactivateCalendarEventAction->execute($calendar);
        });
    }

    public function delete(CalendarEvent $calendar): bool
    {
        return DB::transaction(function () use ($calendar) {
            return $this->deleteCalendarEventAction->execute($calendar);
        });
    }

    public function search(CalendarFilterData $filters, User $user): LengthAwarePaginator
    {
        return $this->calendars->search($filters, $user);
    }

    public function findById(int $id)
    {
        return $this->calendars->findById($id);
    }

}
