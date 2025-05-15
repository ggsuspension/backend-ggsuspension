<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SubcategoryController extends Controller
{
    public function getAllSubcategories(Request $request)
    {
        try {
            $subcategories = Subcategory::with('category')
                ->select('id', 'name', 'category_id',"img_path")
                ->get();

            return response()->json($subcategories->map(function ($subcategory) {
                return [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    "img_path" => $subcategory->img_path,
                    'categoryId' => $subcategory->category_id,
                    'category' => [
                        'id' => $subcategory->category->id,
                        'name' => $subcategory->category->name,
                    ],
                ];
            }));
        } catch (\Exception $e) {
            Log::error('Get All Subcategories failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch subcategories'], 500);
        }
    }

    public function getSubcategoryById(Request $request, $id)
    {
        try {
            $subcategory = Subcategory::with('category')
                ->select('id', 'name', 'category_id')
                ->findOrFail($id);

            return response()->json([
                'id' => $subcategory->id,
                'name' => $subcategory->name,
                'categoryId' => $subcategory->category_id,
                'category' => [
                    'id' => $subcategory->category->id,
                    'name' => $subcategory->category->name,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Get Subcategory failed:', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Subcategory not found'], 404);
        }
    }

    public function createSubcategory(Request $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'categoryId' => 'required|exists:categories,id',
                ]);

                $subcategory = Subcategory::create([
                    'name' => $validated['name'],
                    'category_id' => $validated['categoryId'],
                ]);

                $subcategory->load('category');

                return response()->json([
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'categoryId' => $subcategory->category_id,
                    'category' => [
                        'id' => $subcategory->category->id,
                        'name' => $subcategory->category->name,
                    ],
                ], 201);
            } catch (\Exception $e) {
                Log::error('Create Subcategory failed:', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Failed to create subcategory'], 500);
            }
        });
    }

    public function updateSubcategory(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            try {
                $subcategory = Subcategory::findOrFail($id);

                $validated = $request->validate([
                    'name' => 'sometimes|string|max:255',
                    'categoryId' => 'sometimes|exists:categories,id',
                ]);

                $subcategory->update([
                    'name' => $validated['name'] ?? $subcategory->name,
                    'category_id' => $validated['categoryId'] ?? $subcategory->category_id,
                ]);

                $subcategory->load('category');

                return response()->json([
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'categoryId' => $subcategory->category_id,
                    'category' => [
                        'id' => $subcategory->category->id,
                        'name' => $subcategory->category->name,
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error('Update Subcategory failed:', ['id' => $id, 'error' => $e->getMessage()]);
                return response()->json(['error' => 'Failed to update subcategory'], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
            }
        });
    }

    public function deleteSubcategory(Request $request, $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $subcategory = Subcategory::findOrFail($id);
                $subcategory->delete();
                return response()->json(null, 204);
            } catch (\Exception $e) {
                Log::error('Delete Subcategory failed:', ['id' => $id, 'error' => $e->getMessage()]);
                return response()->json(['error' => 'Failed to delete subcategory'], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
            }
        });
    }
}
