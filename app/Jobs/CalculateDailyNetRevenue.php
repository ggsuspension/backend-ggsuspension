<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\DailyNetRevenue;
use App\Models\Expense;
use App\Models\Gerai;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculateDailyNetRevenue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $geraiId,
        public string $date
    ) {}

    public function handle(): void
    {
        try {
            Log::info("Starting CalculateDailyNetRevenue", [
                'gerai_id' => $this->geraiId,
                'date' => $this->date,
            ]);

            $startOfDay = Carbon::parse($this->date)->startOfDay();
            $endOfDay = $startOfDay->clone()->endOfDay();
            $cacheKey = "daily_net_revenue_{$this->geraiId}_{$startOfDay->toDateString()}";

            Cache::forget($cacheKey);

            $gerai = Gerai::find($this->geraiId);
            if (!$gerai) {
                Log::warning("Gerai not found", ['gerai_id' => $this->geraiId]);
                return;
            }

            $revenueData = Customer::where('gerai', $gerai->name)
                ->where('status', 'FINISH')
                ->whereBetween('customers.updated_at', [$startOfDay, $endOfDay])
                ->select([
                    DB::raw('SUM(COALESCE(harga_service, 0)) as service_total'),
                    DB::raw('SUM(COALESCE(harga_sparepart, 0)) as sparepart_total')
                ])
                ->first();

            $sparepartTotalFromParts = Customer::where('gerai', $gerai->name)
                ->where('status', 'FINISH')
                ->whereBetween('customers.updated_at', [$startOfDay, $endOfDay])
                ->leftJoin('customer_spareparts', 'customers.id', '=', 'customer_spareparts.customer_id')
                ->sum(DB::raw('COALESCE(customer_spareparts.qty * customer_spareparts.price, 0)'));

            $serviceTotal = $revenueData->service_total ?? 0;
            $sparepartTotal = $revenueData->sparepart_total > 0 ? $revenueData->sparepart_total : ($sparepartTotalFromParts ?? 0);
            $totalRevenue = $serviceTotal + $sparepartTotal;

            $totalExpenses = Expense::where('gerai_id', $this->geraiId)
                ->whereBetween('date', [$startOfDay, $endOfDay])
                ->sum('amount') ?? 0;

            $netRevenue = $totalRevenue - $totalExpenses;

            Log::info("Calculation result", [
                'total_revenue' => $totalRevenue,
                'service_total' => $serviceTotal,
                'sparepart_total' => $sparepartTotal,
                'total_expenses' => $totalExpenses,
                'net_revenue' => $netRevenue,
            ]);

            DailyNetRevenue::updateOrCreate(
                [
                    'gerai_id' => $this->geraiId,
                    'date' => $startOfDay->toDateString(),
                ],
                [
                    'total_revenue' => $totalRevenue,
                    'total_expenses' => $totalExpenses,
                    'net_revenue' => $netRevenue,
                ]
            );

            Log::info("DailyNetRevenue updated", [
                'gerai_id' => $this->geraiId,
                'date' => $startOfDay->toDateString(),
                'total_revenue' => $totalRevenue,
                'total_expenses' => $totalExpenses,
                'net_revenue' => $netRevenue,
            ]);
        } catch (\Throwable $e) {
            Log::error("Error in CalculateDailyNetRevenue: " . $e->getMessage(), [
                'gerai_id' => $this->geraiId,
                'date' => $this->date,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
