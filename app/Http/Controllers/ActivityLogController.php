<?php

namespace App\Http\Controllers;

use App\DTOs\ActivityLog\ActivityLogFilterData;
use App\Helpers\ApiResponse;
use App\Http\Requests\ActivityLog\ActivityLogFilterRequest;
use App\Http\Resources\ActivityLogResource;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __construct(
        private ActivityLogService $activityLogs
    ) {}

    public function index(
        ActivityLogFilterRequest $request
    ) {
        $this->authorize(
            'viewAny',
            Activity::class
        );

        $filters = ActivityLogFilterData::fromRequest(
            $request
        );

        $logs = $this->activityLogs->search(
            $filters,
            Auth::user()
        );

        return ApiResponse::paginated(
            ActivityLogResource::collection($logs),
            'Activity logs retrieved successfully.'
        );
    }

    public function show(Activity $activity)
    {
        $this->authorize(
            'view',
            $activity
        );

        return ApiResponse::success(
            new ActivityLogResource($activity),
            'Activity log retrieved successfully.'
        );
    }
}
