<?php

namespace App\Services;

use App\DTOs\ActivityLog\ActivityLogFilterData;
use App\Enums\ActivityLogPermissionEnum;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class ActivityLogService
{
    /**
     * تسجيل Activity مخصصة لعمليات Business
     * مثل:
     * activated
     * deactivated
     * role_changed
     * login
     * logout
     * login_failed
     */
    public function log(
        string $event,
        ?Model $subject = null,
        array $properties = []
    ): Activity {
        $logger = activity();

        if ($subject) {
            $logger->performedOn($subject);
        }

        if (Auth::check()) {
            $logger->causedBy(Auth::user());
        }

        if (! empty($properties)) {
            $logger->withProperties($properties);
        }

        return $logger->log($event);
    }

    /**
     * البحث والفلترة وإظهار السجلات
     */
    public function search(
        ActivityLogFilterData $filters,
        User $user
    ): LengthAwarePaginator {
        $query = Activity::query()
            ->with([
                'causer',
                'subject',
            ]);

        /*
         * المستخدم الذي لا يملك VIEW_ALL
         * يرى فقط الأنشطة التي تسبب بها هو.
         */
        if (! $user->can(
            ActivityLogPermissionEnum::VIEW_ALL->value
        )) {
            $query->where('causer_type', User::class)
                ->where('causer_id', $user->id);
        }

        /*
         * البحث العام
         */
        $query->when(
            $filters->search,
            function ($query, string $search) {
                $query->where(function ($q) use ($search) {
                    $q->where(
                        'description',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'event',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'subject_type',
                        'like',
                        "%{$search}%"
                    );
                });
            }
        );

        /*
         * البحث حسب المستخدم الذي قام بالعملية
         */
        $query->when(
            $filters->causer_id,
            fn ($q, $causerId) =>
                $q->where('causer_id', $causerId)
        );

        /*
         * نوع العملية
         */
        $query->when(
            $filters->event,
            fn ($q, $event) =>
                $q->where('event', $event)
        );

        /*
         * نوع الـModel المتأثر
         */
        $query->when(
            $filters->subject_type,
            fn ($q, $subjectType) =>
                $q->where('subject_type', $subjectType)
        );

        /*
         * ID الـModel المتأثر
         */
        $query->when(
            $filters->subject_id,
            fn ($q, $subjectId) =>
                $q->where('subject_id', $subjectId)
        );

        /*
         * التاريخ
         */
        $query->when(
            $filters->date_from,
            fn ($q, $dateFrom) =>
                $q->whereDate(
                    'created_at',
                    '>=',
                    $dateFrom
                )
        );

        $query->when(
            $filters->date_to,
            fn ($q, $dateTo) =>
                $q->whereDate(
                    'created_at',
                    '<=',
                    $dateTo
                )
        );

        return $query
            ->orderBy(
                $filters->sort_by,
                $filters->sort_dir
            )
            ->paginate(
                $filters->per_page
            );
    }
}
