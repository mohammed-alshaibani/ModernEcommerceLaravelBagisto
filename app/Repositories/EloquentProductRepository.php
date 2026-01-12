<?php

namespace App\Repositories;

use App\Models\Products;
use Illuminate\Database\Eloquent\Collection;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function getAll(): Collection
    {
        return Products::all();
    }

    public function getById(int $id): ?Products
    {
        return Products::find($id);
    }

    public function create(array $data): Products
    {
        return Products::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $Products = Products::find($id);
        if ($Products) {
            return $Products->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $Products = Products::find($id);
        if ($Products) {
            $Products->delete();
            return true;
        }
        return false;
    }

    public function getByVendorId(int $vendorId): Collection
    {
        return Products::where('vendor_id', $vendorId)->get();
    }

    public function getByCategory(int $categoryId): Collection
    {
        return Products::where('category_id', $categoryId)->get();
    }
}