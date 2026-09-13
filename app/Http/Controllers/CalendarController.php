<?php

namespace App\Http\Controllers;

use App\DTOs\Calendar\CalendarFilterData;
use App\DTOs\Calendar\CreateCalendarEventData;
use App\DTOs\Calendar\UpdateCalendarEventData;
use App\Helpers\ApiResponse;
use App\Http\Requests\Calendar\CalendarFilterRequest;
use App\Http\Requests\Calendar\StoreCalendarRequest;
use App\Http\Requests\Calendar\UpdateCalendarRequest;
use App\Http\Resources\CalendarEventResource;
use App\Models\CalendarEvent;
use App\Services\CalendarService;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function __construct(
        private CalendarService $calendar
    ) {}

    public function store(StoreCalendarRequest $request)
    {
        $this->authorize('create', CalendarEvent::class);
        $userId = Auth::id();
        $data = CreateCalendarEventData::fromRequest($request);
        $calendar = $this->calendar->create($data, $userId);

        return ApiResponse::success(
            new CalendarEventResource($calendar),
            "calendar created successfully.",
            201
        );
    }

    public function update(
        UpdateCalendarRequest $request,
        CalendarEvent $calendarEvent
    ) {
        $this->authorize('update', $calendarEvent);

        $dto = UpdateCalendarEventData::fromRequest($request);

        $updatedCalendar = $this->calendar->update(

            $calendarEvent,
            $dto
        );

        return ApiResponse::success(
            new CalendarEventResource($updatedCalendar),
            'Calendar updated successfully.'
        );
    }

    public function destroy(CalendarEvent $calendarEvent)
    {
        $this->authorize('delete', $calendarEvent);

        $this->calendar->delete($calendarEvent);

        return ApiResponse::success(
            null,
            'Calendar event deleted successfully.'
        );
    }

    public function show(CalendarEvent $calendarEvent)
    {
        $this->authorize('view', $calendarEvent);

        return ApiResponse::success(
            new CalendarEventResource($calendarEvent)
        );
    }

    public function index(CalendarFilterRequest $request)
    {
        $this->authorize('viewAny', CalendarEvent::class);

        $dto = CalendarFilterData::fromRequest($request);
        $currentUser = Auth::user();

        $calendar = $this->calendar->search($dto, $currentUser);



        return ApiResponse::paginated(
            CalendarEventResource::collection(
                $calendar
            ),
            "Events retrieved successfully"
        );
    }
    public function activate(CalendarEvent $calendarEvent)
    {
        $this->authorize('activate', $calendarEvent);
        $ativateCalendar = $this->calendar->activate($calendarEvent);
        return ApiResponse::success(
            new CalendarEventResource($ativateCalendar)
        );
    }
    public function deactivate(CalendarEvent $calendarEvent)
    {
        $this->authorize('deactivate', $calendarEvent);
        $deativateCalendar = $this->calendar->deactivate($calendarEvent);
        return ApiResponse::success(
            new CalendarEventResource($deativateCalendar)
        );
    }
}
