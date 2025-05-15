<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function getAllCategories()
    {
        try {
            $categories = Category::all();
            return response()->json($categories);
        } catch (\Exception $e) {
            Log::error('Get All Categories failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch categories'], 500);
        }
    }

    public function getCategoryById(Request $request, $id)
    {
        try {
            $category = Category::select('id', 'name')->findOrFail($id);
            return response()->json([
                'id' => $category->id,
                'name' => $category->name,
            ]);
        } catch (\Exception $e) {
            Log::error('Get Category failed:', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Category not found'], 404);
        }
    }

    public function createCategory(Request $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                ]);

                $category = Category::create([
                    'name' => $validated['name'],
                ]);

                return response()->json([
                    'id' => $category->id,
                    'name' => $category->name,
                ], 201);
            } catch (\Exception $e) {
                Log::error('Create Category failed:', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Failed to create category'], 500);
            }
        });
    }

    public function updateCategory(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            try {
                $category = Category::findOrFail($id);

                $validated = $request->validate([
                    'name' => 'sometimes|string|max:255',
                ]);

                $category->update([
                    'name' => $validated['name'] ?? $category->name,
                ]);

                return response()->json([
                    'id' => $category->id,
                    'name' => $category->name,
                ]);
            } catch (\Exception $e) {
                Log::error('Update Category failed:', ['id' => $id, 'error' => $e->getMessage()]);
                return response()->json(['error' => 'Failed to update category'], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
            }
        });
    }

    public function deleteCategory(Request $request, $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $category = Category::findOrFail($id);
                $category->delete();
                return response()->json(null, 204);
            } catch (\Exception $e) {
                Log::error('Delete Category failed:', ['id' => $id, 'error' => $e->getMessage()]);
                return response()->json(['error' => 'Failed to delete category'], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
            }
        });
    }
}
