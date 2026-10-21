<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Calendar\CalendarFilterData;
use App\Enums\CalendarPermissionEnum;
use App\Models\CalendarEvent;
use App\Models\User;
use App\Repositories\Contracts\CalendarEventRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class CalendarEventRepository extends BaseRepository implements CalendarEventRepositoryInterface
{
    public function __construct(CalendarEvent $calendar)
    {
        $this->model = $calendar;
    }

    public function findByTitle(string $title): ?CalendarEvent
    {
        return $this->model->where('title', $title)->first();
    }
    public function search(CalendarFilterData $filters, User $user): LengthAwarePaginator
    {
        $query = $this->visibleEvents($this->model->newQuery()->with('creator'), $user);
        if ($filters->search) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters->search}%")
                    ->orWhere('description', 'like', "%{$filters->search}%");
            });
        }



        if (! is_null($filters->status)) {
            $query->where('status', $filters->status);
        }

        if ($filters->color) {
            $query->where('color', $filters->color);
        }

        $allowedSortColumns = ['id', 'title', 'start_at', 'end_at', 'created_at'];
        $sortBy = in_array($filters->sort_by, $allowedSortColumns, true)
            ? $filters->sort_by
            : 'start_at';
        $sortDirection = in_array(strtolower($filters->sort_dir), ['asc', 'desc'], true)
            ? strtolower($filters->sort_dir)
            : 'asc';

        return $query
            ->orderBy($sortBy, $sortDirection)
            ->paginate($filters->per_page);
    }

    public function getUpcoming(User $user): Collection
    {
        return $this->visibleEvents($this->model->newQuery()->with('creator'), $user)
            ->where('start_at', '>=', now())
            ->orderBy('start_at')
            ->get();
    }

    private function visibleEvents(Builder $query, User $user): Builder
    {
        if (
            ! $user->isSuperAdmin()
            && ! $user->can(CalendarPermissionEnum::VIEW_ALL->value)
        ) {
            $query->where('created_by', $user->id);
        }

        return $query;
    }
}
