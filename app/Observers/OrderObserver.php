<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Jobs\CalculateDailyNetRevenue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function created(Order $order): void
    {
        if ($order->status === OrderStatus::FINISHED) {
            Log::info("Dispatching CalculateDailyNetRevenue for order created: {$order->id}");
            CalculateDailyNetRevenue::dispatchSync(
                $order->gerai_id,
                $order->created_at->toDateString()
            );
        }
    }

    public function updated(Order $order): void
    {
        if ($order->wasChanged('status') && $order->status === OrderStatus::FINISHED) {
            Log::info("Dispatching CalculateDailyNetRevenue for order updated: {$order->id}");
            CalculateDailyNetRevenue::dispatchSync(
                $order->gerai_id,
                $order->created_at->toDateString()
            );
        }
    }
}
