<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\Categories;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService; // Corrected service name
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends Controller
{
    private $Productervice;

    public function __construct(ProductService $Productervice)
    {
        $this->Productervice = $Productervice;
    }

    public function index()
    {
        try {
            $Products = $this->Productervice->getAllProducts();
            return response()->json([
                'success' => true,
                'data' => $Products->map(fn($Products) => $this->transformProducts($Products)),
                'message' => 'Products retrieved successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Products',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->validated();
            $data['Products_image'] = $request->file('Products_image');
            $Products = $this->Productervice->createProducts($data);

            return response()->json([
                'success' => true,
                'data' => $this->transformProducts($Products),
                'message' => 'Products created successfully'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Products',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Products $Products)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->transformProducts($Products),
                'message' => 'Products retrieved successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Products',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateProductRequest $request, Products $Products)
    {
        try {
            $data = $request->validated();
            $data['Products_image'] = $request->file('Products_image');
            $this->Productervice->updateProducts($Products->id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Products updated successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Products',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Products $Products)
    {
        try {
            $this->Productervice->deleteProducts($Products->id);
            return response()->json([
                'success' => true,
                'message' => 'Products deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Products',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function transformProducts(Products $Products): array
    {
        return [
            'id' => $Products->id,
            'vendor_id' => $Products->vendor_id,
            'category_id' => $Products->category_id,
            'name' => $Products->name,
            'slug' => $Products->slug,
            'description' => $Products->description,
            'price' => $Products->price,
            'stock_quantity' => $Products->stock_quantity,
            'Products_image' => $Products->getImageUrlAttribute(),
            'is_active' => $Products->is_active,
            'created_at' => $Products->created_at,
            'updated_at' => $Products->updated_at,
        ];
    }
}