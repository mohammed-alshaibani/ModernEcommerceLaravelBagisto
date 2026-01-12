<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Product\Services;

use MyPlatform\EcommerceCore\Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function getAllProducts(): Collection
    {
        return $this->productRepository->all();
    }

    public function createProduct(array $data): Model
    {
        // Business logic can go here (e.g. validate SKU uniqueness, stock check)
        return $this->productRepository->create($data);
    }
}
