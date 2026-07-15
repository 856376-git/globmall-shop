<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct()
    {
        $this->model = app($this->model());
    }

    abstract public function model(): string;

    public function find(int $id, array $with = []): ?Model
    {
        return $this->model->with($with)->find($id);
    }

    public function paginate(int $perPage = 12, array $with = [], string $orderBy = 'id', string $direction = 'desc'): LengthAwarePaginator
    {
        return $this->model->with($with)->orderBy($orderBy, $direction)->paginate($perPage);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }
}
