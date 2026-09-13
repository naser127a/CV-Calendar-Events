<?php

namespace App\Repositories\Contracts;

use App\DTOs\Calendar\CalendarFilterData;
use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CalendarEventRepositoryInterface extends BaseRepositoryInterface
{
    public function findByTitle(string $title): ?CalendarEvent;

    public function search(CalendarFilterData $filters, User $user): LengthAwarePaginator;

    public function getUpcoming(): Collection;
}
