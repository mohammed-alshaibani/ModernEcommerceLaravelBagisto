<?php

namespace App\Http\Controllers;

use App\Models\DeliveryPerson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class DeliveryPersonController extends Controller
{
    public function index()
    {
        try {
            $deliveryPersons = DeliveryPerson::all();
            return response()->json([
                'success' => true,
                'data' => $deliveryPersons->map(fn($deliveryPerson) => $this->transformDeliveryPerson($deliveryPerson)),
                'message' => 'Delivery persons retrieved successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve delivery persons',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'vehicle_type' => 'required|string|max:255',
        ]);

        try {
            $deliveryPerson = DeliveryPerson::create($validatedData);
            return response()->json([
                'success' => true,
                'data' => $this->transformDeliveryPerson($deliveryPerson),
                'message' => 'Delivery person created successfully'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create delivery person',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(DeliveryPerson $deliveryPerson)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->transformDeliveryPerson($deliveryPerson),
                'message' => 'Delivery person retrieved successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve delivery person',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, DeliveryPerson $deliveryPerson)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'vehicle_type' => 'required|string|max:255',
        ]);

        try {
            $deliveryPerson->update($validatedData);
            return response()->json([
                'success' => true,
                'message' => 'Delivery person updated successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update delivery person',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(DeliveryPerson $deliveryPerson)
    {
        try {
            $deliveryPerson->delete();
            return response()->json([
                'success' => true,
                'message' => 'Delivery person deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete delivery person',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function transformDeliveryPerson(DeliveryPerson $deliveryPerson): array
    {
        return [
            'id' => $deliveryPerson->id,
            'name' => $deliveryPerson->name,
            'phone' => $deliveryPerson->phone,
            'vehicle_type' => $deliveryPerson->vehicle_type,
            'is_available' => $deliveryPerson->is_available,
            'created_at' => $deliveryPerson->created_at,
            'updated_at' => $deliveryPerson->updated_at,
        ];
    }
}