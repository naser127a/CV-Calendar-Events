<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository
{
    protected Model $model;

    public function findById(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function getAll(): Collection
    {
        return $this->model
            ->orderBy('id')
            ->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->paginate($perPage);
    }

    public function create(array $data): Model
    {
        return $this->model
            ->create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model->fresh();
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
