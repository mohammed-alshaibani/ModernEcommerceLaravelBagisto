<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Orders;
use App\Models\DeliveryPerson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class DeliveryController extends Controller
{
    public function index()
    {
        try {
            $deliveries = Delivery::all();
            return response()->json([
                'success' => true,
                'data' => $deliveries->map(fn($delivery) => $this->transformDelivery($delivery)),
                'message' => 'Deliveries retrieved successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve deliveries',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'Orders_id' => 'required|exists:Orders,id',
            'delivery_person_id' => 'nullable|exists:delivery_persons,id',
            'status' => 'required|in:pending,assigned,picked_up,en_route,delivered,cancelled',
            'pickup_time' => 'nullable|date',
            'delivery_time' => 'nullable|date',
        ]);

        try {
            $delivery = Delivery::create($validatedData);
            return response()->json([
                'success' => true,
                'data' => $this->transformDelivery($delivery),
                'message' => 'Delivery created successfully'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create delivery',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Delivery $delivery)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->transformDelivery($delivery),
                'message' => 'Delivery retrieved successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve delivery',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, Delivery $delivery)
    {
        $validatedData = $request->validate([
            'Orders_id' => 'required|exists:Orders,id',
            'delivery_person_id' => 'nullable|exists:delivery_persons,id',
            'status' => 'required|in:pending,assigned,picked_up,en_route,delivered,cancelled',
            'pickup_time' => 'nullable|date',
            'delivery_time' => 'nullable|date',
        ]);

        try {
            $delivery->update($validatedData);
            return response()->json([
                'success' => true,
                'message' => 'Delivery updated successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update delivery',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Delivery $delivery)
    {
        try {
            $delivery->delete();
            return response()->json([
                'success' => true,
                'message' => 'Delivery deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete delivery',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function transformDelivery(Delivery $delivery): array
    {
        return [
            'id' => $delivery->id,
            'Orders_id' => $delivery->Orders_id,
            'delivery_person_id' => $delivery->delivery_person_id,
            'status' => $delivery->status,
            'pickup_time' => $delivery->pickup_time,
            'delivery_time' => $delivery->delivery_time,
            'created_at' => $delivery->created_at,
            'updated_at' => $delivery->updated_at,
        ];
    }
}