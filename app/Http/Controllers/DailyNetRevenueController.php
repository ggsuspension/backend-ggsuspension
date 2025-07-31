<?php

namespace App\Http\Controllers;

use App\Jobs\CalculateDailyNetRevenue;
use App\Models\Customer;
use App\Models\DailyNetRevenue;
use App\Models\Gerai;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DailyNetRevenueController extends Controller
{
    public function createDailyNetRevenue(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'gerai_id' => 'required|exists:gerais,id',
            'date' => 'required|date',
            'total_revenue' => 'required|numeric|min:0',
            'total_expenses' => 'required|numeric|min:0',
        ]);

        try {
            $netRevenue = $validated['total_revenue'] - $validated['total_expenses'];

            $revenue = DailyNetRevenue::updateOrCreate(
                [
                    'gerai_id' => $validated['gerai_id'],
                    'date' => Carbon::parse($validated['date'])->startOfDay(),
                ],
                [
                    'total_revenue' => $validated['total_revenue'],
                    'total_expenses' => $validated['total_expenses'],
                    'net_revenue' => $netRevenue,
                ]
            );

            return response()->json($revenue->load('gerai'), 201);
        } catch (\Throwable $e) {
            Log::error('Error creating DailyNetRevenue: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat DailyNetRevenue'], 500);
        }
    }

    public function calculateDailyNetRevenue(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'batchSize' => 'nullable|integer|min:1',
        ]);

        try {
            $date = Carbon::parse($validated['date'])->startOfDay();
            $batchSize = $validated['batchSize'] ?? 50;

            $geraiList = Gerai::pluck('id')->toArray();

            foreach (array_chunk($geraiList, $batchSize) as $batch) {
                foreach ($batch as $geraiId) {
                    CalculateDailyNetRevenue::dispatch($geraiId, $date->toDateString());
                }
            }

            return response()->json([
                'message' => 'Perhitungan DailyNetRevenue untuk semua gerai telah dijadwalkan.',
            ], 202);
        } catch (\Throwable $e) {
            Log::error('Error scheduling DailyNetRevenue calculation: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menjadwalkan perhitungan DailyNetRevenue'], 500);
        }
    }

    public function getOrCalculateDailyNetRevenue(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'gerai_id' => 'nullable|exists:gerais,id',
        ]);

        try {
            $date = Carbon::parse($validated['date'])->startOfDay();
            $endOfDay = $date->copy()->endOfDay();
            $geraiId = $validated['gerai_id'] ?? null;

            $geraiList = $geraiId ? [Gerai::find($geraiId)] : Gerai::all();
            if (empty($geraiList)) {
                return response()->json(['message' => 'Tidak ada gerai ditemukan'], 404);
            }

            $results = [];
            foreach ($geraiList as $gerai) {
                if (!$gerai) continue;

                $totalRevenue = Customer::where('gerai', $gerai->name)
                    ->where('status', 'FINISH')
                    ->whereBetween('updated_at', [$date, $endOfDay])
                    ->select(DB::raw('SUM(COALESCE(harga_service, 0) + COALESCE(harga_sparepart, 0)) as total'))
                    ->value('total') ?? 0;

                $totalExpenses = Expense::where('gerai_id', $gerai->id)
                    ->whereBetween('date', [$date, $endOfDay])
                    ->sum('amount') ?? 0;

                $netRevenue = $totalRevenue - $totalExpenses;

                $revenue = DailyNetRevenue::updateOrCreate(
                    [
                        'gerai_id' => $gerai->id,
                        'date' => $date->toDateString(),
                    ],
                    [
                        'total_revenue' => $totalRevenue,
                        'total_expenses' => $totalExpenses,
                        'net_revenue' => $netRevenue,
                    ]
                );

                $results[] = $revenue->load('gerai');
            }

            $data = collect($results)->map(fn($item) => [
                'date' => $item->date->toDateString(),
                'gerai' => $item->gerai->name ?? 'Unknown',
                'net_revenue' => number_format($item->net_revenue, 2, '.', ''),
            ]);

            return response()->json(['data' => $data]);
        } catch (\Throwable $e) {
            Log::error('Error in getOrCalculateDailyNetRevenue: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghitung data harian'], 500);
        }
    }

    public function getDailyTrend(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'gerai_id' => 'required|exists:gerais,id',
        ]);

        try {
            $startDate = Carbon::parse($validated['startDate'])->startOfDay();
            $endDate = Carbon::parse($validated['endDate'])->endOfDay();

            Log::info('getDailyTrend called', [
                'startDate' => $startDate->toDateTimeString(),
                'endDate' => $endDate->toDateTimeString(),
                'gerai_id' => $validated['gerai_id'],
                'request_ip' => $request->ip(),
            ]);

            $data = DailyNetRevenue::where('gerai_id', $validated['gerai_id'])
                ->whereBetween('date', [$startDate, $endDate])
                ->with('gerai')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'gerai_id' => $item->gerai_id,
                        'date' => $item->date->toIso8601String(),
                        'total_revenue' => number_format($item->total_revenue, 2, '.', ''),
                        'total_expenses' => number_format($item->total_expenses, 2, '.', ''),
                        'net_revenue' => number_format($item->net_revenue, 2, '.', ''),
                        'created_at' => $item->created_at->toIso8601String(),
                        'updated_at' => $item->updated_at->toIso8601String(),
                        'gerai' => $item->gerai ? [
                            'id' => $item->gerai->id,
                            'name' => $item->gerai->name,
                            'location' => $item->gerai->location,
                            'created_at' => $item->gerai->created_at->toIso8601String(),
                            'updated_at' => $item->gerai->updated_at->toIso8601String(),
                        ] : null,
                    ];
                });

            Log::info('getDailyTrend result', [
                'data' => $data->toArray(),
                'total_items' => $data->count(),
            ]);

            return response()->json([
                'data' => $data,
                'pagination' => [
                    'page' => 1,
                    'limit' => 50,
                    'totalItems' => $data->count(),
                    'totalPages' => 1,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in getDailyTrend: ' . $e->getMessage(), [
                'startDate' => $validated['startDate'],
                'endDate' => $validated['endDate'],
                'gerai_id' => $validated['gerai_id'],
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Gagal mengambil data tren harian'], 500);
        }
    }

    public function dailyTrendAll(Request $request)
    {
        try {
            $validated = $request->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date|after_or_equal:startDate',
            ]);

            $startDate = Carbon::parse($validated['startDate'])->startOfDay();
            $endDate = Carbon::parse($validated['endDate'])->endOfDay();
            $cacheKey = "daily_trend_all_{$startDate->toDateString()}_{$endDate->toDateString()}";

            Log::info('dailyTrendAll called', [
                'startDate' => $startDate->toDateString(),
                'endDate' => $endDate->toDateString(),
                'request_ip' => $request->ip(),
            ]);

            Cache::forget($cacheKey); // Hapus cache untuk data terbaru

            $data = DailyNetRevenue::query()
                ->whereBetween('date', [$startDate, $endDate])
                ->select('date', 'net_revenue', 'gerai_id')
                ->with('gerai:id,name')
                ->get();

            Log::info('DailyNetRevenue query result', [
                'count' => $data->count(),
                'data' => $data->toArray(),
            ]);

            $groupedData = $data->groupBy('gerai_id')->map(function ($group, $geraiId) {
                $geraiName = $group->first()->gerai ? $group->first()->gerai->name : 'Unknown';
                return [
                    'gerai_id' => (int) $geraiId,
                    'gerai' => $geraiName,
                    'data' => $group->map(function ($item) {
                        return [
                            'date' => $item->date->toDateString(),
                            'netRevenue' => (float) $item->net_revenue,
                        ];
                    })->values(),
                ];
            })->values();

            Log::info('dailyTrendAll result', [
                'data' => $groupedData->toArray(),
                'total_items' => $groupedData->count(),
            ]);

            return response()->json(['data' => $groupedData]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in dailyTrendAll', [
                'errors' => $e->errors(),
                'startDate' => $request->query('startDate'),
                'endDate' => $request->query('endDate'),
            ]);
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Error in dailyTrendAll: ' . $e->getMessage(), [
                'startDate' => $request->query('startDate'),
                'endDate' => $request->query('endDate'),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Gagal mengambil tren harian'], 500);
        }
    }

    public function getIncomeExpenseDaily(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'gerai_id' => 'required|exists:gerais,id',
        ]);

        try {
            $date = Carbon::parse($validated['date'])->startOfDay();
            $geraiId = $validated['gerai_id'];

            Log::info('getIncomeExpenseDaily called', [
                'date' => $date->toDateString(),
                'gerai_id' => $geraiId,
            ]);

            // Coba ambil data dari tabel
            $revenue = DailyNetRevenue::where('date', $date->toDateString())
                ->where('gerai_id', $geraiId)
                ->with('gerai')
                ->first();

            // Jika data tidak ada, hitung ulang
            if (!$revenue) {
                $gerai = Gerai::find($geraiId);
                if (!$gerai) {
                    Log::error('Gerai tidak ditemukan', ['gerai_id' => $geraiId]);
                    return response()->json(['error' => 'Gerai tidak ditemukan'], 404);
                }

                $endOfDay = $date->copy()->endOfDay();

                // Hitung total pendapatan dari customers
                $totalRevenue = Customer::where('gerai', $gerai->name)
                    ->where('status', 'FINISH')
                    ->whereBetween('updated_at', [$date, $endOfDay])
                    ->select(DB::raw('SUM(COALESCE(harga_service, 0) + COALESCE(harga_sparepart, 0)) as total'))
                    ->value('total') ?? 0;

                // Hitung total pengeluaran dari expenses
                $totalExpenses = Expense::where('gerai_id', $geraiId)
                    ->whereBetween('date', [$date, $endOfDay])
                    ->sum('amount') ?? 0;

                Log::info('Calculated data', [
                    'total_revenue' => $totalRevenue,
                    'total_expenses' => $totalExpenses,
                ]);

                // Hitung pendapatan bersih
                $netRevenue = $totalRevenue - $totalExpenses;

                // Simpan atau perbarui data
                $revenue = DailyNetRevenue::updateOrCreate(
                    [
                        'gerai_id' => $geraiId,
                        'date' => $date->toDateString(),
                    ],
                    [
                        'total_revenue' => $totalRevenue,
                        'total_expenses' => $totalExpenses,
                        'net_revenue' => $netRevenue,
                    ]
                );
            }

            // Format response
            $response = [
                'gerai_id' => $revenue->gerai_id,
                'gerai' => $revenue->gerai ? $revenue->gerai->name : 'Unknown',
                'date' => $revenue->date->toDateString(),
                'total_revenue' => number_format($revenue->total_revenue, 2, '.', ''),
                'total_expenses' => number_format($revenue->total_expenses, 2, '.', ''),
                'net_revenue' => number_format($revenue->net_revenue, 2, '.', ''),
            ];

            Log::info('getIncomeExpenseDaily response', ['data' => $response]);

            return response()->json(['data' => $response]);
        } catch (\Throwable $e) {
            Log::error('Error in getIncomeExpenseDaily: ' . $e->getMessage(), [
                'gerai_id' => $geraiId,
                'date' => $validated['date'],
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Gagal mengambil data pendapatan dan pengeluaran'], 500);
        }
    }

    public function getTotalRevenue(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'geraiId' => 'nullable|exists:gerais,id',
        ]);

        try {
            $startDate = $validated['startDate'];
            $endDate = $validated['endDate'];

            // Konversi YY-MM-DD ke YYYY-MM-DD
            if (preg_match('/^\d{2}-\d{2}-\d{2}$/', $startDate)) {
                $startDate = '20' . $startDate; // Asumsikan abad 21
            }
            if (preg_match('/^\d{2}-\d{2}-\d{2}$/', $endDate)) {
                $endDate = '20' . $endDate;
            }

            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $query = DailyNetRevenue::whereBetween('date', [$start, $end])
                ->selectRaw('gerai_id, SUM(total_revenue) as total_revenue');

            if (!empty($validated['geraiId'])) {
                $query->where('gerai_id', $validated['geraiId']);
            } else {
                $query->groupBy('gerai_id');
            }

            $revenues = $query->get()
                ->map(fn($item) => [
                    'gerai_id' => $item->gerai_id,
                    'total_revenue' => $item->total_revenue ?? 0,
                ]);

            return response()->json(['data' => $revenues]);
        } catch (\Throwable $e) {
            Log::error('Error fetching total revenue: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghitung total pendapatan'], 500);
        }
    }

    public function getTotalGrossRevenue(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'geraiId' => 'required|exists:gerais,id',
        ]);

        try {
            $startDate = $validated['startDate'];
            $endDate = $validated['endDate'];
            $geraiId = $validated['geraiId'];

            // Konversi YY-MM-DD ke YYYY-MM-DD jika perlu
            if (preg_match('/^\d{2}-\d{2}-\d{2}$/', $startDate)) {
                $startDate = '20' . $startDate;
            }
            if (preg_match('/^\d{2}-\d{2}-\d{2}$/', $endDate)) {
                $endDate = '20' . $endDate;
            }

            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $gerai = Gerai::find($geraiId);
            if (!$gerai) {
                return response()->json(['error' => 'Gerai tidak ditemukan'], 404);
            }

            $totalRevenue = Customer::where('gerai', $gerai->name)
                ->where('status', 'FINISH')
                ->whereBetween('updated_at', [$start, $end])
                ->selectRaw('SUM(COALESCE(harga_service, 0) + COALESCE(harga_sparepart, 0)) as total')
                ->value('total') ?? 0;

            Log::info('getTotalGrossRevenue result', [
                'gerai_id' => $geraiId,
                'startDate' => $start->toDateString(),
                'endDate' => $end->toDateString(),
                'total_revenue' => $totalRevenue,
            ]);

            return response()->json(['totalRevenue' => $totalRevenue]);
        } catch (\Throwable $e) {
            Log::error('Error in getTotalGrossRevenue: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghitung total pendapatan kotor'], 500);
        }
    }

    public function getPeriodicTrend(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'gerai_id' => 'required|exists:gerais,id',
            'period' => 'required|in:weekly,monthly,yearly',
            'date' => 'required|date',
        ]);

        try {
            $geraiId = $validated['gerai_id'];
            $period = $validated['period'];
            $referenceDate = Carbon::parse($validated['date'])->endOfDay();
            $startDate = $referenceDate->copy();
            $previousEndDate = $referenceDate->copy();
            $previousStartDate = $referenceDate->copy();

            switch ($period) {
                case 'weekly':
                    $startDate->subDays(6);
                    $previousEndDate = $startDate->copy()->subDay();
                    $previousStartDate = $previousEndDate->copy()->subDays(6);
                    break;
                case 'monthly':
                    $startDate->startOfMonth();
                    $previousStartDate = $startDate->copy()->subMonth()->startOfMonth();
                    $previousEndDate = $previousStartDate->copy()->endOfMonth();
                    break;
                case 'yearly':
                    $startDate->startOfYear();
                    $previousStartDate = $startDate->copy()->subYear()->startOfYear();
                    $previousEndDate = $previousStartDate->copy()->endOfYear();
                    break;
            }
            $currentPeriod = DailyNetRevenue::where('gerai_id', $geraiId)
                ->whereBetween('date', [$startDate, $referenceDate])
                ->sum('net_revenue');

            $previousPeriod = DailyNetRevenue::where('gerai_id', $geraiId)
                ->whereBetween('date', [$previousStartDate, $previousEndDate])
                ->sum('net_revenue');

            $difference = $currentPeriod - $previousPeriod;
            $trend = $difference > 0 ? 'Naik' : ($difference < 0 ? 'Turun' : 'Stagnan');
            $percentageChange = $previousPeriod != 0
                ? round(($difference / $previousPeriod) * 100, 2)
                : null;

            return response()->json([
                'current_period' => [
                    'start' => $startDate->toDateString(),
                    'end' => $referenceDate->toDateString(),
                    'net_revenue' => $currentPeriod,
                ],
                'previous_period' => [
                    'start' => $previousStartDate->toDateString(),
                    'end' => $previousEndDate->toDateString(),
                    'net_revenue' => $previousPeriod,
                ],
                'trend' => $trend,
                'difference' => $difference,
                'percentage_change' => $percentageChange,
            ]);
        } catch (\Exception $e) {
            Log::error('Error calculating periodic trend: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghitung tren periode'], 500);
        }
    }


    public function getDailyNetRevenue(Request $request, $id): JsonResponse
    {
        try {
            $revenue = DailyNetRevenue::with('gerai')->findOrFail($id);
            return response()->json($revenue);
        } catch (\Exception $e) {
            Log::error('Error fetching DailyNetRevenue: ' . $e->getMessage());
            return response()->json(['error' => 'DailyNetRevenue tidak ditemukan'], 404);
        }
    }

    public function getAllDailyNetRevenues(Request $request): JsonResponse
    {
        try {
            $revenues = DailyNetRevenue::with('gerai')->get();
            return response()->json($revenues);
        } catch (\Exception $e) {
            Log::error('Error fetching all DailyNetRevenues: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengambil semua DailyNetRevenues'], 500);
        }
    }

    public function updateDailyNetRevenue(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'gerai_id' => 'nullable|exists:gerais,id',
            'date' => 'nullable|date',
            'total_revenue' => 'nullable|numeric|min:0',
            'total_expenses' => 'nullable|numeric|min:0',
        ]);

        try {
            $revenue = DailyNetRevenue::findOrFail($id);

            $totalRevenue = $validated['total_revenue'] ?? $revenue->total_revenue;
            $totalExpenses = $validated['total_expenses'] ?? $revenue->total_expenses;
            $netRevenue = $totalRevenue - $totalExpenses;

            $revenue->update([
                'gerai_id' => $validated['gerai_id'] ?? $revenue->gerai_id,
                'date' => isset($validated['date']) ? Carbon::parse($validated['date'])->startOfDay() : $revenue->date,
                'total_revenue' => $totalRevenue,
                'total_expenses' => $totalExpenses,
                'net_revenue' => $netRevenue,
            ]);

            return response()->json($revenue->load('gerai'));
        } catch (\Exception $e) {
            Log::error('Error updating DailyNetRevenue: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memperbarui DailyNetRevenue'], 500);
        }
    }

    public function deleteDailyNetRevenue(Request $request, $id): JsonResponse
    {
        try {
            $revenue = DailyNetRevenue::findOrFail($id);
            $revenue->delete();
            return response()->json(['message' => 'DailyNetRevenue berhasil dihapus']);
        } catch (\Exception $e) {
            Log::error('Error deleting DailyNetRevenue: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghapus DailyNetRevenue'], 500);
        }
    }

    public function getRevenueByPeriod(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'gerai_id' => 'required|exists:gerais,id',
            'period' => 'required|in:daily,monthly,yearly,all',
            'date' => 'required_if:period,daily,monthly,yearly|date', // Wajib untuk daily, monthly, yearly
        ]);

        try {
            $geraiId = $validated['gerai_id'];
            $period = $validated['period'];
            $gerai = Gerai::find($geraiId);

            if (!$gerai) {
                return response()->json(['error' => 'Gerai tidak ditemukan'], 404);
            }

            $query = Customer::where('gerai', $gerai->name)
                ->where('status', 'FINISH')
                ->selectRaw('SUM(COALESCE(harga_service, 0) + COALESCE(harga_sparepart, 0)) as total_revenue');

            // Tentukan rentang waktu berdasarkan periode
            if ($period === 'daily') {
                $date = Carbon::parse($validated['date'])->startOfDay();
                $endOfDay = $date->copy()->endOfDay();
                $query->whereBetween('updated_at', [$date, $endOfDay]);
            } elseif ($period === 'monthly') {
                $date = Carbon::parse($validated['date']);
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();
                $query->whereBetween('updated_at', [$startOfMonth, $endOfMonth]);
            } elseif ($period === 'yearly') {
                $date = Carbon::parse($validated['date']);
                $startOfYear = $date->copy()->startOfYear();
                $endOfYear = $date->copy()->endOfYear();
                $query->whereBetween('updated_at', [$startOfYear, $endOfYear]);
            } elseif ($period === 'all') {
                // Tidak ada filter tanggal untuk keseluruhan
            }

            $totalRevenue = $query->value('total_revenue') ?? 0;

            // Ambil total pengeluaran (expenses) berdasarkan periode
            $expenseQuery = Expense::where('gerai_id', $geraiId);
            if ($period === 'daily') {
                $expenseQuery->whereBetween('date', [$date, $endOfDay]);
            } elseif ($period === 'monthly') {
                $expenseQuery->whereBetween('date', [$startOfMonth, $endOfMonth]);
            } elseif ($period === 'yearly') {
                $expenseQuery->whereBetween('date', [$startOfYear, $endOfYear]);
            } elseif ($period === 'all') {
                // Tidak ada filter tanggal untuk keseluruhan
            }

            $totalExpenses = $expenseQuery->sum('amount') ?? 0;

            // Hitung net revenue
            $netRevenue = $totalRevenue - $totalExpenses;

            // Simpan atau perbarui DailyNetRevenue untuk periode harian
            if ($period === 'daily') {
                DailyNetRevenue::updateOrCreate(
                    [
                        'gerai_id' => $geraiId,
                        'date' => $date->toDateString(),
                    ],
                    [
                        'total_revenue' => $totalRevenue,
                        'total_expenses' => $totalExpenses,
                        'net_revenue' => $netRevenue,
                    ]
                );
            }

            // Format response
            $response = [
                'gerai' => $gerai->name,
                'period' => $period,
                'total_revenue' => number_format($totalRevenue, 2, '.', ''),
                'total_expenses' => number_format($totalExpenses, 2, '.', ''),
                'net_revenue' => number_format($netRevenue, 2, '.', ''),
            ];

            if ($period !== 'all') {
                $response['date'] = $validated['date'];
            }

            return response()->json(['data' => $response]);
        } catch (\Throwable $e) {
            Log::error('Error in getRevenueByPeriod: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghitung pendapatan berdasarkan periode'], 500);
        }
    }
}
