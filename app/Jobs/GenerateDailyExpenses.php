<?php

namespace App\Jobs;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Gerai;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateDailyExpenses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $date;

    public function __construct($date)
    {
        $this->date = Carbon::parse($date)->startOfDay();
    }

    public function handle()
    {
        try {
            DB::transaction(function () {
                $categories = ExpenseCategory::query()
                    ->where(function ($query) {
                        $query->whereNotNull('daily_cost')
                            ->orWhereNotNull('monthly_cost');
                    })
                    ->orWhereHas('gerais', function ($query) {
                        $query->whereNotNull('daily_cost')
                            ->orWhereNotNull('monthly_cost');
                    })
                    ->get();

                $gerais = Gerai::select('id', 'name')->get();
                $daysInMonth = $this->date->daysInMonth;

                foreach ($categories as $category) {
                    foreach ($gerais as $gerai) {
                        $dailyAmount = 0;

                        // Cek biaya spesifik di expense_category_gerai
                        $pivot = $category->gerais()->where('gerai_id', $gerai->id)->first();

                        if ($pivot && ($pivot->pivot->daily_cost || $pivot->pivot->monthly_cost)) {
                            // Gunakan biaya dari pivot
                            if ($pivot->pivot->daily_cost) {
                                $dailyAmount += $pivot->pivot->daily_cost;
                            }
                            if ($pivot->pivot->monthly_cost) {
                                $dailyAmount += $pivot->pivot->monthly_cost / $daysInMonth;
                            }
                        } else {
                            // Fallback ke biaya di ExpenseCategory
                            if ($category->daily_cost) {
                                $dailyAmount += $category->daily_cost;
                            }
                            if ($category->monthly_cost) {
                                $dailyAmount += $category->monthly_cost / $daysInMonth;
                            }
                        }

                        if ($dailyAmount > 0) {
                            $expense = Expense::create([
                                'gerai_id' => $gerai->id,
                                'category' => $category->name,
                                'description' => "Biaya harian otomatis: {$category->name}",
                                'amount' => round($dailyAmount, 2),
                                'date' => $this->date,
                            ]);

                            Log::info("Generated expense for category: {$category->name}, gerai: {$gerai->name}, amount: {$dailyAmount}", [
                                'expense_id' => $expense->id,
                                'date' => $this->date->toDateString(),
                            ]);

                            // Dispatch job untuk menghitung ulang daily_net_revenues
                            CalculateDailyNetRevenue::dispatchSync($gerai->id, $this->date->toDateString());
                        }
                    }
                }
            });
        } catch (\Throwable $e) {
            Log::error("Error in GenerateDailyExpenses: " . $e->getMessage(), [
                'date' => $this->date->toDateString(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
