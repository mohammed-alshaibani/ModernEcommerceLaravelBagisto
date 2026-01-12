<?php

namespace App\Repositories;

use App\Models\Products;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function getAll(): Collection;
    public function getById(int $id): ?Products;
    public function create(array $data): Products;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getByVendorId(int $vendorId): Collection;
    public function getByCategory(int $categoryId): Collection; // Filter by category
}