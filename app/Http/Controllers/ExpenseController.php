<?php

namespace App\Http\Controllers;

use App\Jobs\CalculateDailyNetRevenue;
use App\Models\DailyNetRevenue;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function getAllExpenses(Request $request)
    {
        try {
            $data = Expense::where("gerai_id", $request->gerai_id)->whereBetween('date', [$request->start_date, $request->end_date])->get();
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Get All Expenses failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getExpenseById(Request $request, $id)
    {
        try {
            $expense = Expense::with(['gerai', 'expenseCategory'])
                ->select('id', 'gerai_id', 'expense_category_id', 'description', 'amount', 'date', 'created_at', 'updated_at')
                ->findOrFail($id);

            $netRevenue = $expense->gerai_id ? DailyNetRevenue::where('gerai_id', $expense->gerai_id)
                ->where('date', $expense->date->format('Y-m-d'))
                ->first() : null;

            return response()->json([
                'id' => $expense->id,
                'geraiId' => $expense->gerai_id,
                'expenseCategoryId' => $expense->expense_category_id,
                'description' => $expense->description,
                'amount' => (float) $expense->amount,
                'date' => $expense->date->format('Y-m-d\TH:i:s.v\Z'),
                'gerai' => $expense->gerai ? [
                    'id' => $expense->gerai->id,
                    'name' => $expense->gerai->name,
                    'location' => $expense->gerai->location,
                    'totalRevenue' => $netRevenue ? (float) $netRevenue->total_revenue : 0,
                    'totalExpenses' => $netRevenue ? (float) $netRevenue->total_expenses : 0,
                    'netRevenue' => $netRevenue ? (float) $netRevenue->net_revenue : 0,
                    'createdAt' => $expense->gerai->created_at->format('Y-m-d\TH:i:s.v\Z'),
                    'updatedAt' => $expense->gerai->updated_at->format('Y-m-d\TH:i:s.v\Z'),
                ] : null,
                'expenseCategory' => [
                    'id' => $expense->expenseCategory->id,
                    'name' => $expense->expenseCategory->name,
                    'createdAt' => $expense->expenseCategory->created_at->format('Y-m-d\TH:i:s.v\Z'),
                    'updatedAt' => $expense->expenseCategory->updated_at->format('Y-m-d\TH:i:s.v\Z'),
                ],
                'createdAt' => $expense->created_at->format('Y-m-d\TH:i:s.v\Z'),
                'updatedAt' => $expense->updated_at->format('Y-m-d\TH:i:s.v\Z'),
            ]);
        } catch (\Exception $e) {
            Log::error('Get Expense failed:', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Pengeluaran tidak ditemukan'], 404);
        }
    }

    public function createExpense(Request $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                $validated = $request->validate([
                    'geraiId' => 'nullable|exists:gerais,id',
                    'category' => 'required|string',
                    'description' => 'nullable|string|max:255',
                    'amount' => 'required|numeric|min:0',
                    'date' => 'required|date',
                ]);

                $expense = Expense::create([
                    'gerai_id' => $validated['geraiId'],
                    'category' => $validated['category'],
                    'description' => $validated['description'] ?? 'Biaya tanpa deskripsi',
                    'amount' => $validated['amount'],
                    'date' => Carbon::parse($validated['date'])->startOfDay(),
                ]);

                if ($validated['geraiId']) {
                    // Ubah ke dispatchSync untuk real-time
                    CalculateDailyNetRevenue::dispatchSync($validated['geraiId'], $validated['date']);
                }

                return response()->json([
                    'id' => $expense->id,
                    'geraiId' => $expense->gerai_id,
                    'category' => $expense->category,
                    'description' => $expense->description,
                    'amount' => (float) $expense->amount,
                    'date' => $expense->date->format('Y-m-d\TH:i:s.v\Z'),
                    'gerai' => $expense->gerai ? [
                        'id' => $expense->gerai->id,
                        'name' => $expense->gerai->name,
                        'location' => $expense->gerai->location,
                        'totalRevenue' => (float) ($expense->gerai->total_revenue ?? 0),
                        'totalExpenses' => (float) ($expense->gerai->total_expenses ?? 0),
                        'netRevenue' => (float) ($expense->gerai->net_revenue ?? 0),
                        'createdAt' => $expense->gerai->created_at->format('Y-m-d\TH:i:s.v\Z'),
                        'updatedAt' => $expense->gerai->updated_at->format('Y-m-d\TH:i:s.v\Z'),
                    ] : null,
                    'createdAt' => $expense->created_at->format('Y-m-d\TH:i:s.v\Z'),
                    'updatedAt' => $expense->updated_at->format('Y-m-d\TH:i:s.v\Z'),
                ], 201);
            } catch (\Exception $e) {
                Log::error('Create expense failed:', ['error' => $e->getMessage()]);
                return response()->json(['error' => $e], 500);
            }
        });
    }

    public function updateExpense(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            try {
                $expense = Expense::findOrFail($id);

                $validated = $request->validate([
                    'geraiId' => 'nullable|exists:gerais,id',
                    'expenseCategoryId' => 'sometimes|exists:expense_categories,id',
                    'description' => 'sometimes|nullable|string',
                    'amount' => 'sometimes|numeric|min:0',
                    'date' => 'sometimes|date',
                ]);

                $oldDate = $expense->date;
                $oldGeraiId = $expense->gerai_id;

                $expense->update([
                    'gerai_id' => $validated['geraiId'] ?? $expense->gerai_id,
                    'expense_category_id' => $validated['expenseCategoryId'] ?? $expense->expense_category_id,
                    'description' => $validated['description'] ?? $expense->description,
                    'amount' => $validated['amount'] ?? $expense->amount,
                    'date' => isset($validated['date']) ? Carbon::parse($validated['date'])->startOfDay() : $expense->date,
                ]);

                // Dispatch job untuk gerai_id lama dan baru jika relevan
                if (
                    isset($validated['amount']) ||
                    isset($validated['date']) ||
                    (isset($validated['geraiId']) && $validated['geraiId'] !== $oldGeraiId)
                ) {
                    if ($oldGeraiId) {
                        CalculateDailyNetRevenue::dispatch($oldGeraiId, $oldDate->toDateString());
                    }
                    if ($expense->gerai_id) {
                        CalculateDailyNetRevenue::dispatch($expense->gerai_id, $expense->date->toDateString());
                    }
                }

                $expense->load(['gerai', 'expenseCategory']);
                $netRevenue = $expense->gerai_id ? DailyNetRevenue::where('gerai_id', $expense->gerai_id)
                    ->where('date', $expense->date->format('Y-m-d'))
                    ->first() : null;

                return response()->json([
                    'id' => $expense->id,
                    'geraiId' => $expense->gerai_id,
                    'expenseCategoryId' => $expense->expense_category_id,
                    'description' => $expense->description,
                    'amount' => (float) $expense->amount,
                    'date' => $expense->date->format('Y-m-d\TH:i:s.v\Z'),
                    'gerai' => $expense->gerai ? [
                        'id' => $expense->gerai->id,
                        'name' => $expense->gerai->name,
                        'location' => $expense->gerai->location,
                        'totalRevenue' => $netRevenue ? (float) $netRevenue->total_revenue : 0,
                        'totalExpenses' => $netRevenue ? (float) $netRevenue->total_expenses : 0,
                        'netRevenue' => $netRevenue ? (float) $netRevenue->net_revenue : 0,
                        'createdAt' => $expense->gerai->created_at->format('Y-m-d\TH:i:s.v\Z'),
                        'updatedAt' => $expense->gerai->updated_at->format('Y-m-d\TH:i:s.v\Z'),
                    ] : null,
                    'expenseCategory' => [
                        'id' => $expense->expenseCategory->id,
                        'name' => $expense->expenseCategory->name,
                        'createdAt' => $expense->expenseCategory->created_at->format('Y-m-d\TH:i:s.v\Z'),
                        'updatedAt' => $expense->expenseCategory->updated_at->format('Y-m-d\TH:i:s.v\Z'),
                    ],
                    'createdAt' => $expense->created_at->format('Y-m-d\TH:i:s.v\Z'),
                    'updatedAt' => $expense->updated_at->format('Y-m-d\TH:i:s.v\Z'),
                ]);
            } catch (\Exception $e) {
                Log::error('Update Expense failed:', ['id' => $id, 'error' => $e->getMessage()]);
                return response()->json(['error' => 'Gagal memperbarui pengeluaran'], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
            }
        });
    }

    public function deleteExpense(Request $request, $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $expense = Expense::findOrFail($id);

                $geraiId = $expense->gerai_id;
                $date = $expense->date->toDateString();

                $expense->delete();

                // Dispatch job jika gerai_id tidak null
                if ($geraiId) {
                    CalculateDailyNetRevenue::dispatch($geraiId, $date);
                }

                return response()->json(null, 204);
            } catch (\Exception $e) {
                Log::error('Delete Expense failed:', ['id' => $id, 'error' => $e->getMessage()]);
                return response()->json(['error' => 'Gagal menghapus pengeluaran'], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
            }
        });
    }
}
