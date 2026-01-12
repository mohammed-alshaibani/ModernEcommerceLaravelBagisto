<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class OrdersController extends Controller
{
    public function index()
    {
        try {
            $Orders = Orders::all();
            return response()->json([
                'success' => true,
                'data' => $Orders->map(fn($Orders) => $this->transformOrders($Orders)),
                'message' => 'Orders retrieved successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Orders',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'shipping_address' => 'required|string',
            'billing_address' => 'required|string',
            'payment_method' => 'required|string',
            'cart' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $Orders = Orders::create([
                'user_id' => auth()->id(),
                'Orders_number' => uniqid('ORD-'),
                'total_amount' => 0,
                'shipping_address' => $validatedData['shipping_address'],
                'billing_address' => $validatedData['billing_address'],
                'payment_method' => $validatedData['payment_method'],
                'status' => 'pending',
            ]);

            $totalAmount = 0;
            foreach ($validatedData['cart'] as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                $quantity = $item['quantity'];
                if (!$product) {
                    throw new \Exception('Product not found: '.$item['product_id']);
                }
                $Orders->products()->attach($product->id, ['quantity' => $quantity, 'price' => $product->price]);
                $totalAmount += $product->price * $quantity;
            }
            $Orders->update(['total_amount' => $totalAmount]);

            DB::commit();
            return response()->json([
                'success' => true,
                'data' => $this->transformOrders($Orders),
                'message' => 'Orders created successfully'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Orders',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Orders $Orders)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->transformOrders($Orders),
                'message' => 'Orders retrieved successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Orders',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     public function update(Request $request, Orders $Orders)
    {
        $validatedData = $request->validate([
           'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        try {
            $Orders->update($validatedData);

            return response()->json([
                'success' => true,
                'data' => $this->transformOrders($Orders),
                'message' => 'Orders updated successfully'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to update Orders',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Orders $Orders)
    {
        try {
            $Orders->delete();
            return response()->json([
                'success' => true,
                'message' => 'Orders deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Orders',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function transformOrders(Orders $Orders): array
    {
        return [
            'id' => $Orders->id,
            'user_id' => $Orders->user_id,
            'Orders_number' => $Orders->Orders_number,
            'total_amount' => $Orders->total_amount,
            'shipping_address' => $Orders->shipping_address,
            'billing_address' => $Orders->billing_address,
            'payment_method' => $Orders->payment_method,
            'status' => $Orders->status,
            'created_at' => $Orders->created_at,
            'updated_at' => $Orders->updated_at,
             'products' => $Orders->products->map(function ($product) {  // Include Orders item details
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $product->pivot->quantity,  // Get the quantity from the pivot table
                ];
            }),
        ];
    }
}