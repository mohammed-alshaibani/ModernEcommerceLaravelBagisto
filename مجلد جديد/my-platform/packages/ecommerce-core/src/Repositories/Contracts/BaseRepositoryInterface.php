<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface BaseRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Model;
    public function create(array $data): Model;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
