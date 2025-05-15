<?php

namespace App\Http\Controllers;

use App\Jobs\CalculateDailyNetRevenue;
use App\Models\DailyNetRevenue;
use App\Models\Gerai;
use App\Models\Order;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        } catch (\Exception $e) {
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
            $geraiList = Gerai::select('id')->get()->toArray();

            // Dispatch job untuk setiap gerai
            foreach (array_chunk($geraiList, $batchSize) as $batch) {
                foreach ($batch as $gerai) {
                    CalculateDailyNetRevenue::dispatch($gerai['id'], $date->toDateString());
                }
            }

            return response()->json([
                'message' => 'Perhitungan DailyNetRevenue untuk semua gerai telah dijadwalkan.',
            ], 202); // Status 202 Accepted karena proses berjalan di latar belakang
        } catch (\Exception $e) {
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

            if ($geraiId) {
                $totalRevenue = Order::where('gerai_id', $geraiId)
                    ->whereBetween('created_at', [$date, $endOfDay])
                    ->where('status', 'FINISHED')
                    ->sum('total_harga');

                $totalExpenses = Expense::where('gerai_id', $geraiId)
                    ->whereBetween('date', [$date, $endOfDay])
                    ->sum('amount');

                $netRevenue = $totalRevenue - $totalExpenses;

                $revenue = DailyNetRevenue::updateOrCreate(
                    [
                        'gerai_id' => $geraiId,
                        'date' => $date,
                    ],
                    [
                        'total_revenue' => $totalRevenue,
                        'total_expenses' => $totalExpenses,
                        'net_revenue' => $netRevenue,
                    ]
                );

                $results = [$revenue];
            } else {
                $geraiList = Gerai::select('id')->get()->toArray();
                $results = [];

                foreach ($geraiList as $gerai) {
                    $totalRevenue = Order::where('gerai_id', $gerai['id'])
                        ->whereBetween('created_at', [$date, $endOfDay])
                        ->where('status', 'FINISHED')
                        ->sum('total_harga');

                    $totalExpenses = Expense::where('gerai_id', $gerai['id'])
                        ->whereBetween('date', [$date, $endOfDay])
                        ->sum('amount');

                    $netRevenue = $totalRevenue - $totalExpenses;

                    $revenue = DailyNetRevenue::updateOrCreate(
                        [
                            'gerai_id' => $gerai['id'],
                            'date' => $date,
                        ],
                        [
                            'total_revenue' => $totalRevenue,
                            'total_expenses' => $totalExpenses,
                            'net_revenue' => $netRevenue,
                        ]
                    );

                    $results[] = $revenue;
                }
            }

            $data = DailyNetRevenue::with('gerai')
                ->whereBetween('date', [$date, $endOfDay])
                ->when($geraiId, fn($q) => $q->where('gerai_id', $geraiId))
                ->get()
                ->map(fn($revenue) => [
                    'date' => $revenue->date->toDateString(),
                    'gerai' => $revenue->gerai->name ?? 'Unknown',
                    'net_revenue' => $revenue->net_revenue ?? 0,
                ]);

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            Log::error('Error in getOrCalculateDailyNetRevenue: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghitung data harian'], 500);
        }
    }

    public function getDailyTrend(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'gerai_id' => 'nullable|integer|exists:gerais,id',
        ]);
        $data = DailyNetRevenue::where('daily_net_revenues.gerai_id', $validated['gerai_id']);

        return response()->json([
            'data' => $data,
            'pagination' => [
                'page' => 1,
                'limit' => $data->count(),
                'totalItems' => $data->count(),
                'totalPages' => 1,
            ],
        ]);
    }


    public function getIncomeExpenseDaily(Request $request): JsonResponse
    {
        try {
            $data = DailyNetRevenue::where('date', $request->date)->where('gerai_id', $request->gerai_id)->get()->toArray();
            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getTotalRevenue(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
        ]);

        try {
            $start = Carbon::parse($validated['startDate'])->startOfDay();
            $end = Carbon::parse($validated['endDate'])->endOfDay();
            $revenues = DailyNetRevenue::whereBetween('date', [$start, $end])
                ->groupBy('gerai_id')
                ->selectRaw('gerai_id, SUM(total_revenue) as total_revenue')
                ->get()
                ->map(fn($revenue) => [
                    'gerai_id' => $revenue->gerai_id,
                    'total_revenue' => $revenue->total_revenue ?? 0,
                ]);
            return response()->json(['data' => $revenues]);
        } catch (\Exception $e) {
            Log::error('Error fetching total revenue: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghitung total pendapatan'], 500);
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
            $endDate = Carbon::parse($validated['date'])->endOfDay();
            $startDate = $endDate->copy();
            $previousStartDate = $endDate->copy();
            $previousEndDate = $endDate->copy();

            switch ($period) {
                case 'weekly':
                    $startDate->subDays(6);
                    $previousEndDate = $startDate->copy()->subDay();
                    $previousStartDate = $previousEndDate->copy()->subDays(6);
                    break;
                case 'monthly':
                    $startDate->subMonth();
                    $previousEndDate = $startDate->copy()->subDay();
                    $previousStartDate = $previousEndDate->copy()->subMonth();
                    break;
                case 'yearly':
                    $startDate->subYear();
                    $previousEndDate = $startDate->copy()->subDay();
                    $previousStartDate = $previousEndDate->copy()->subYear();
                    break;
            }

            $currentPeriod = DailyNetRevenue::where('gerai_id', $geraiId)
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('net_revenue');

            $previousPeriod = DailyNetRevenue::where('gerai_id', $geraiId)
                ->whereBetween('date', [$previousStartDate, $previousEndDate])
                ->sum('net_revenue');

            $difference = $currentPeriod - $previousPeriod;
            $trend = $difference > 0 ? 'Naik' : ($difference < 0 ? 'Turun' : 'Stabil');

            return response()->json([
                'data' => [
                    'gerai_id' => $geraiId,
                    'period' => $period,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'net_revenue' => $currentPeriod ?? 0,
                    'previous_net_revenue' => $previousPeriod ?? 0,
                    'difference' => $difference,
                    'trend' => $trend,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching periodic trend: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengambil tren periodik'], 500);
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
            'net_revenue' => 'nullable|numeric',
        ]);

        try {
            $revenue = DailyNetRevenue::findOrFail($id);

            $totalRevenue = $validated['total_revenue'] ?? $revenue->total_revenue;
            $totalExpenses = $validated['total_expenses'] ?? $revenue->total_expenses;
            $netRevenue = $validated['net_revenue'] ?? ($totalRevenue - $totalExpenses);

            $revenue->update([
                'gerai_id' => $validated['gerai_id'] ?? $revenue->gerai_id,
                'date' => $validated['date'] ? Carbon::parse($validated['date'])->startOfDay() : $revenue->date,
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
}
