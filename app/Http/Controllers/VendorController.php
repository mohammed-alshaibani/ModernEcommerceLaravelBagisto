<?php

namespace App\Http\Controllers;



use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;

class VendorController extends Controller
{
    public function index()
    {
        try {
            $vendors = Vendor::all();
            return response()->json([
                'success' => true,
                'data' => $vendors->map(fn($vendor) => $this->transformVendor($vendor)),
                'message' => 'Vendors retrieved successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vendors',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors',
            'description' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            // Create a new user for the vendor
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => bcrypt('password'),
            ]);

            // Handle store logo upload
            if ($request->hasFile('store_logo')) {
                $image = $request->file('store_logo');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $path = Storage::disk('public')->putFileAs('logos', $image, $filename);
                $validatedData['store_logo'] = 'logos/' . $filename;
            }

            $vendor = Vendor::create([
                'user_id' => $user->id,
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'description' => $validatedData['description'],
                'phone' => $validatedData['phone'],
                'address' => $validatedData['address'],
                'store_logo' => $validatedData['store_logo'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'data' => $this->transformVendor($vendor),
                'message' => 'Vendor created successfully',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            if ($user) {
                $user->delete();
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to create vendor',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Vendor $vendor)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->transformVendor($vendor),
                'message' => 'Vendor retrieved successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vendor',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email,'.$vendor->id,
            'description' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            // Handle store logo upload
            if ($request->hasFile('store_logo')) {
                $image = $request->file('store_logo');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $path = Storage::disk('public')->putFileAs('logos', $image, $filename);
                $validatedData['store_logo'] = 'logos/' . $filename;
            }

            $vendor->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Vendor updated successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update vendor',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Vendor $vendor)
    {
        try {
            $vendor->delete();
            return response()->json([
                'success' => true,
                'message' => 'Vendor deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete vendor',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function transformVendor(Vendor $vendor): array
    {
        return [
            'id' => $vendor->id,
            'user_id' => $vendor->user_id,
            'name' => $vendor->name,
            'slug' => $vendor->slug,
            'description' => $vendor->description,
            'email' => $vendor->email,
            'phone' => $vendor->phone,
            'address' => $vendor->address,
            'store_logo' => $vendor->getLogoUrlAttribute(),
            'is_active' => $vendor->is_active,
            'created_at' => $vendor->created_at,
            'updated_at' => $vendor->updated_at,
        ];
    }
}