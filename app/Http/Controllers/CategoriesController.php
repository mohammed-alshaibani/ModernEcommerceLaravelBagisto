<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class CategoriesController extends Controller
{
    public function index()
    {
        try {
            $categories = Categories::get()->toTree();
            return response()->json([
                'success' => true,
                'data' => $categories->map(fn($Categories) => $this->transformCategories($Categories)),
                'message' => 'Categories retrieved successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        try {
            $Categories = new Categories(['name' => $validatedData['name']]);
            if ($request->filled('parent_id')) {
                $parent = Categories::find($validatedData['parent_id']);
                $parent->appendNode($Categories);
            } else {
                $Categories->save();
            }
            return response()->json([
                'success' => true,
                'data' => $this->transformCategories($Categories),
                'message' => 'Categories created successfully'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Categories',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Categories $Categories)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->transformCategories($Categories),
                'message' => 'Categories retrieved successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Categories',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, Categories $Categories)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        try {
            $Categories->update(['name' => $validatedData['name']]);
            if ($request->filled('parent_id')) {
                $parent = Categories::find($validatedData['parent_id']);
                $Categories->moveToNode($parent);
            } else {
                $Categories->makeRoot();
            }
            return response()->json([
                'success' => true,
                'message' => 'Categories updated successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Categories',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Categories $Categories)
    {
        try {
            $Categories->delete();
            return response()->json([
                'success' => true,
                'message' => 'Categories deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Categories',
                'errors' => [$e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function transformCategories(Categories $Categories): array
    {
        return [
            'id' => $Categories->id,
            'name' => $Categories->name,
            'slug' => $Categories->slug,
            'description' => $Categories->description,
            'parent_id' => $Categories->parent_id,
            'created_at' => $Categories->created_at,
            'updated_at' => $Categories->updated_at,
        ];
    }
}