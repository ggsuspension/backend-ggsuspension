<?php

namespace App\Jobs;

use App\Models\DailyNetRevenue;
use App\Models\Gerai;
use App\Models\Order;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CalculateDailyNetRevenue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $geraiId;
    protected $date;

    /**
     * Create a new job instance.
     */
    public function __construct(int $geraiId, string $date)
    {
        $this->geraiId = $geraiId;
        $this->date = $date;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $startOfDay = Carbon::parse($this->date)->startOfDay();
            $endOfDay = $startOfDay->copy()->endOfDay();

            // Hitung total pendapatan dari order
            $totalRevenue = Order::where('gerai_id', $this->geraiId)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->where('status', 'FINISHED')
                ->sum('total_harga');

            // Hitung total pengeluaran dari expense
            $totalExpenses = Expense::where('gerai_id', $this->geraiId)
                ->whereBetween('date', [$startOfDay, $endOfDay])
                ->sum('amount');

            $netRevenue = $totalRevenue - $totalExpenses;

            // Simpan atau perbarui DailyNetRevenue
            DailyNetRevenue::updateOrCreate(
                [
                    'gerai_id' => $this->geraiId,
                    'date' => $startOfDay,
                ],
                [
                    'total_revenue' => $totalRevenue,
                    'total_expenses' => $totalExpenses,
                    'net_revenue' => $netRevenue,
                ]
            );

            Log::info("DailyNetRevenue calculated for gerai_id: {$this->geraiId}, date: {$startOfDay->toDateString()}");
        } catch (\Exception $e) {
            Log::error("Error calculating DailyNetRevenue for gerai_id: {$this->geraiId}: " . $e->getMessage());
            throw $e; // Biarkan job gagal untuk ditangani ulang (jika configured)
        }
    }
}
