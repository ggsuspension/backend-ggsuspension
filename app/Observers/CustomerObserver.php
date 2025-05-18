<?php

namespace App\Observers;

use App\Jobs\CalculateDailyNetRevenue;
use App\Models\Customer;
use App\Models\Gerai;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CustomerObserver
{
    public function created(Customer $customer): void
    {
        Log::debug("Customer created", [
            'customer_id' => $customer->id,
            'gerai' => $customer->gerai,
            'status' => $customer->status,
            'created_at' => $customer->created_at->toDateTimeString(),
        ]);

        if ($customer->status === 'FINISH' && !empty(trim($customer->gerai))) {
            $gerai = Gerai::where('name', trim($customer->gerai))->first();
            if ($gerai) {
                Log::info("Dispatching CalculateDailyNetRevenue", [
                    'customer_id' => $customer->id,
                    'gerai_id' => $gerai->id,
                    'date' => $customer->created_at->toDateString(),
                ]);
                CalculateDailyNetRevenue::dispatchSync(
                    $gerai->id,
                    $customer->created_at->toDateString()
                );
            } else {
                Log::warning("Gerai not found", [
                    'customer_id' => $customer->id,
                    'gerai_name' => $customer->gerai,
                ]);
            }
        }
    }

    public function updated(Customer $customer): void
    {
        $originalStatus = $customer->getOriginal('status');
        $newStatus = $customer->status;

        if ($originalStatus !== 'FINISH' && $newStatus === 'FINISH' && !empty(trim($customer->gerai))) {
            $gerai = Gerai::where('name', trim($customer->gerai))->first();
            if ($gerai) {
                Log::info("Dispatching CalculateDailyNetRevenue", [
                    'customer_id' => $customer->id,
                    'gerai_id' => $gerai->id,
                    'date' => $customer->updated_at->toDateString(),
                ]);
                CalculateDailyNetRevenue::dispatchSync(
                    $gerai->id,
                    $customer->updated_at->toDateString()
                );
            } else {
                Log::warning("Gerai not found", [
                    'customer_id' => $customer->id,
                    'gerai_name' => $customer->gerai,
                ]);
            }
        }
    }
}
