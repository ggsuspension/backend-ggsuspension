<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ExpenseCategoryController extends Controller
{
    public function getAllExpenseCategories(Request $request)
    {
        try {
            $categories = ExpenseCategory::select('id', 'name', 'created_at', 'updated_at')->get();
            return response()->json($categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'createdAt' => $category->created_at->format('Y-m-d\TH:i:s.v\Z'),
                    'updatedAt' => $category->updated_at->format('Y-m-d\TH:i:s.v\Z'),
                ];
            }));
        } catch (\Exception $e) {
            Log::error('Get All Expense Categories failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch expense categories'], 500);
        }
    }

    public function getExpenseCategoryById(Request $request, $id)
    {
        try {
            $category = ExpenseCategory::select('id', 'name', 'created_at', 'updated_at')->findOrFail($id);
            return response()->json([
                'id' => $category->id,
                'name' => $category->name,
                'createdAt' => $category->created_at->format('Y-m-d\TH:i:s.v\Z'),
                'updatedAt' => $category->updated_at->format('Y-m-d\TH:i:s.v\Z'),
            ]);
        } catch (\Exception $e) {
            Log::error('Get Expense Category failed:', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Expense category not found'], 404);
        }
    }

    public function createExpenseCategory(Request $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                $validated = $request->validate([
                    'name' => 'required|string|max:255|unique:expense_categories,name',
                    'daily_cost' => 'nullable|numeric|min:0',
                    'monthly_cost' => 'nullable|numeric|min:0',
                ]);

                $category = ExpenseCategory::create([
                    'name' => $validated['name'],
                    'daily_cost' => $validated['daily_cost'],
                    'monthly_cost' => $validated['monthly_cost'],
                ]);

                return response()->json([
                    'id' => $category->id,
                    'name' => $category->name,
                    'daily_cost' => $category->daily_cost !== null ? (float) $category->daily_cost : null,
                    'monthly_cost' => $category->monthly_cost !== null ? (float) $category->monthly_cost : null,
                    'createdAt' => $category->created_at->format('Y-m-d\TH:i:s.v\Z'),
                    'updatedAt' => $category->updated_at->format('Y-m-d\TH:i:s.v\Z'),
                ], 201);
            } catch (\Exception $e) {
                Log::error('Create Expense Category failed:', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Gagal membuat kategori pengeluaran'], 500);
            }
        });
    }

    public function updateExpenseCategory(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            try {
                $category = ExpenseCategory::findOrFail($id);

                $validated = $request->validate([
                    'name' => 'sometimes|string|max:255|unique:expense_categories,name,' . $id,
                    'daily_cost' => 'sometimes|nullable|numeric|min:0',
                    'monthly_cost' => 'sometimes|nullable|numeric|min:0',
                ]);

                $category->update([
                    'name' => $validated['name'] ?? $category->name,
                    'daily_cost' => isset($validated['daily_cost']) ? $validated['daily_cost'] : $category->daily_cost,
                    'monthly_cost' => isset($validated['monthly_cost']) ? $validated['monthly_cost'] : $category->monthly_cost,
                ]);

                return response()->json([
                    'id' => $category->id,
                    'name' => $category->name,
                    'daily_cost' => $category->daily_cost !== null ? (float) $category->daily_cost : null,
                    'monthly_cost' => $category->monthly_cost !== null ? (float) $category->monthly_cost : null,
                    'createdAt' => $category->created_at->format('Y-m-d\TH:i:s.v\Z'),
                    'updatedAt' => $category->updated_at->format('Y-m-d\TH:i:s.v\Z'),
                ]);
            } catch (\Exception $e) {
                Log::error('Update Expense Category failed:', ['id' => $id, 'error' => $e->getMessage()]);
                return response()->json(['error' => 'Gagal memperbarui kategori pengeluaran'], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
            }
        });
    }

    public function deleteExpenseCategory(Request $request, $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $category = ExpenseCategory::findOrFail($id);
                $category->delete();
                return response()->json(null, 204);
            } catch (\Exception $e) {
                Log::error('Delete Expense Category failed:', ['id' => $id, 'error' => $e->getMessage()]);
                return response()->json(['error' => 'Failed to delete expense category'], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
            }
        });
    }
}
