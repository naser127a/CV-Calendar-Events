<?php

namespace App\Repositories\Contracts;

use App\DTOs\News\NewsFilterData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface NewsRepositoryInterface extends BaseRepositoryInterface
{

public function search(NewsFilterData $filters): LengthAwarePaginator;
}
