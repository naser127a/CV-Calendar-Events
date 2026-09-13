<?php

namespace App\Services;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected BaseRepositoryInterface $repository
    ) {}

    public function findById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function getAll(): Collection
    {
        return $this->repository->getAll();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }
}
