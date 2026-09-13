<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Calendar\CalendarFilterData;
use App\Models\CalendarEvent;
use App\Models\User;
use App\Repositories\Contracts\CalendarEventRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

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
        $query = $this->model->newQuery()->with('creator');
        if (! $user->hasRole('admin')) {
            $query->where('created_by', $user->id);
        }
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

        return $query
            ->orderBy($filters->sort_by, $filters->sort_dir)
            ->paginate($filters->per_page);
    }

    public function getUpcoming(): Collection
    {
        return $this->model
            ->where('start_at', '>=', now())
            ->orderBy('start_at')
            ->get();
    }
}
