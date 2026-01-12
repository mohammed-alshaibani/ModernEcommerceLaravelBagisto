<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductService
{
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function createProduct(array $data): Products
    {
        $data['slug'] = Str::slug($data['name']); // Generate slug

        if (isset($data['product_image'])) {
            $image = $data['product_image'];
            $filename = time() . '.' . $image->getClientOriginalExtension();
            Image::make($image)->resize(300, 300)->save(storage_path('app/public/products/' . $filename));
            $data['product_image'] = 'products/' . $filename; // Store the path
        }

        return $this->productRepository->create($data);
    }

    public function updateProduct(int $id, array $data): bool
    {
        $product = $this->productRepository->getById($id);
        if (!$product) {
            return false; // Product not found
        }

        $data['slug'] = Str::slug($data['name']);

        if (isset($data['product_image'])) {
             //  TODO: Delete the old image if it exists (clean up storage)

            $image = $data['product_image'];
            $filename = time() . '.' . $image->getClientOriginalExtension();
            Image::make($image)->resize(300, 300)->save(storage_path('app/public/products/' . $filename));
            $data['product_image'] = 'products/' . $filename;
        }

        return $this->productRepository->update($id, $data);
    }

    public function getAllProducts(): Collection
    {
        return $this->productRepository->getAll();
    }

    public function getProductById(int $id): ?Product
    {
        return $this->productRepository->getById($id);
    }

    public function getProductsByVendor(int $vendorId): Collection
    {
        return $this->productRepository->getByVendorId($vendorId);
    }

    public function getProductsByCategory(int $categoryId): Collection
    {
        return $this->productRepository->getByCategory($categoryId);
    }

    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }
}