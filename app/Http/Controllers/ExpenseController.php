<?php

namespace App\Http\Controllers;

use App\Jobs\CalculateDailyNetRevenue;
use App\Models\DailyNetRevenue;
use App\Models\Expense;
use App\Models\Gerai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class ExpenseController extends Controller
{
    public function getAllExpenses(Request $request)
    {
        try {
            $geraiId = $request->query('gerai_id');
            $startDate = $request->query('startDate');
            $endDate = $request->query('endDate');

            // Validasi parameter
            if (!$geraiId || !$startDate || !$endDate) {
                Log::error('Missing required parameters for getAllExpenses', [
                    'gerai_id' => $geraiId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);
                return response()->json(['error' => 'Missing required parameters'], 400);
            }

            // Konversi tanggal untuk memastikan format yang benar
            $startDate = \Carbon\Carbon::parse($startDate)->startOfDay();
            $endDate = \Carbon\Carbon::parse($endDate)->endOfDay();

            Log::info('Fetching expenses with parameters:', [
                'gerai_id' => $geraiId,
                'start_date' => $startDate->toDateTimeString(),
                'end_date' => $endDate->toDateTimeString(),
            ]);

            $data = Expense::where('gerai_id', $geraiId)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            Log::info('Expenses fetched:', [
                'count' => $data->count(),
                'data' => $data->toArray(),
            ]);

            // Bungkus data dalam format yang konsisten
            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            Log::error('Get All Expenses failed:', [
                'error' => $e->getMessage(),
                'gerai_id' => $geraiId ?? null,
                'start_date' => $startDate ?? null,
                'end_date' => $endDate ?? null,
            ]);
            return response()->json(['error' => 'Failed to fetch expenses: ' . $e->getMessage()], 500);
        }
    }
    public function getTotalExpenses()
    {
        $data = Expense::all();
        return response()->json($data);
    }

    public function getAllExpensesAll(Request $request)
    {
        try {
            $validated = $request->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date|after_or_equal:startDate',
            ]);

            $startDate = Carbon::parse($validated['startDate'])->startOfDay();
            $endDate = Carbon::parse($validated['endDate'])->endOfDay();
            $cacheKey = "expenses_all_{$startDate->toDateString()}_{$endDate->toDateString()}";

            Log::info('getAllExpensesAll called', [
                'startDate' => $startDate->toDateString(),
                'endDate' => $endDate->toDateString(),
                'request_ip' => $request->ip(),
                'user_role' => JWTAuth::user()->role ?? 'unknown',
            ]);

            Cache::forget($cacheKey);

            $expenses = Expense::whereBetween('date', [$startDate, $endDate])
                ->select('id', 'gerai_id', 'amount', 'description', 'date', 'category', 'created_at', 'updated_at')
                ->get();

            Log::info('Expenses query result', [
                'count' => $expenses->count(),
                'data' => $expenses->toArray(),
            ]);

            $groupedData = $expenses->groupBy('gerai_id')->map(function ($group, $geraiId) {
                $gerai = Gerai::find($geraiId);
                return [
                    'gerai_id' => (int) $geraiId,
                    'gerai' => $gerai ? $gerai->name : 'Unknown',
                    'data' => $group->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'gerai_id' => $item->gerai_id,
                            'amount' => (float) $item->amount,
                            'description' => $item->description,
                            'date' => $item->date->toDateTimeString(),
                            'category' => $item->category,
                            'created_at' => $item->created_at->toDateTimeString(),
                            'updated_at' => $item->updated_at->toDateTimeString(),
                        ];
                    })->values(),
                ];
            })->values();

            Log::info('getAllExpensesAll result', [
                'data' => $groupedData->toArray(),
                'total_items' => $groupedData->count(),
            ]);

            return response()->json(['data' => $groupedData]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in getAllExpensesAll', [
                'errors' => $e->errors(),
                'startDate' => $request->query('startDate'),
                'endDate' => $request->query('endDate'),
            ]);
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Error in getAllExpensesAll: ' . $e->getMessage(), [
                'startDate' => $request->query('startDate'),
                'endDate' => $request->query('endDate'),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Gagal mengambil data pengeluaran semua gerai'], 500);
        }
    }


    public function createExpense(Request $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                Log::info("Creating expense", ['request' => $request->all()]);
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
                    Log::info("Dispatching CalculateDailyNetRevenue", [
                        'geraiId' => $validated['geraiId'],
                        'date' => $validated['date'],
                    ]);
                    CalculateDailyNetRevenue::dispatchSync($validated['geraiId'], $validated['date']);
                }

                Log::info("Expense created successfully", ['expense_id' => $expense->id]);

                return response()->json([
                    'id' => $expense->id,
                    'geraiId' => $expense->gerai_id,
                    'category' => $expense->category,
                    'description' => $expense->description,
                    'amount' => (float) $expense->amount,
                    'date' => $expense->date->format('Y-m-d\TH:i:s.v\Z'),
                ], 201);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Validation failed:', ['errors' => $e->errors(), 'request' => $request->all()]);
                return response()->json(['error' => $e->errors()], 422);
            } catch (\Exception $e) {
                Log::error('Create expense failed:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all(),
                ]);
                return response()->json(['error' => $e->getMessage()], 500);
            }
        });
    }

    public function updateExpense(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            try {
                Log::info("Updating expense", ['id' => $id, 'request' => $request->all()]);

                $validated = $request->validate([
                    'geraiId' => 'nullable|exists:gerais,id',
                    'category' => 'required|string',
                    'description' => 'nullable|string|max:255',
                    'amount' => 'required|numeric|min:0',
                    'date' => 'required|date',
                ]);

                $expense = Expense::findOrFail($id);

                $oldGeraiId = $expense->gerai_id;
                $oldDate = $expense->date->toDateString();

                $expense->update([
                    'gerai_id' => $validated['geraiId'],
                    'category' => $validated['category'],
                    'description' => $validated['description'] ?? 'Biaya tanpa deskripsi',
                    'amount' => $validated['amount'],
                    'date' => Carbon::parse($validated['date'])->startOfDay(),
                ]);

                // Trigger update pendapatan harian jika gerai_id atau date berubah
                $newGeraiId = $validated['geraiId'];
                $newDate = Carbon::parse($validated['date'])->toDateString();

                if ($oldGeraiId && $oldDate !== $newDate) {
                    CalculateDailyNetRevenue::dispatch($oldGeraiId, $oldDate);
                }
                if ($newGeraiId) {
                    CalculateDailyNetRevenue::dispatch($newGeraiId, $newDate);
                }

                Log::info("Expense updated successfully", ['id' => $expense->id]);

                return response()->json([
                    'id' => $expense->id,
                    'geraiId' => $expense->gerai_id,
                    'category' => $expense->category,
                    'description' => $expense->description,
                    'amount' => (float) $expense->amount,
                    'date' => $expense->date->format('Y-m-d\TH:i:s.v\Z'),
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Validation failed in updateExpense', ['errors' => $e->errors(), 'request' => $request->all()]);
                return response()->json(['error' => $e->errors()], 422);
            } catch (\Exception $e) {
                Log::error('Update expense failed', [
                    'id' => $id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return response()->json(['error' => 'Gagal memperbarui pengeluaran'], 500);
            }
        });
    }


    public function deleteExpense($id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $expense = Expense::findOrFail($id);
                $geraiId = $expense->gerai_id;
                $date = $expense->date->toDateString();

                $expense->delete();

                if ($geraiId) {
                    CalculateDailyNetRevenue::dispatch($geraiId, $date);
                }

                return response()->json(['message' => 'Pengeluaran berhasil dihapus']);
            } catch (\Exception $e) {
                Log::error('Failed to delete expense', [
                    'id' => $id,
                    'error' => $e->getMessage(),
                ]);
                return response()->json(['error' => 'Gagal menghapus pengeluaran'], 500);
            }
        });
    }
}
