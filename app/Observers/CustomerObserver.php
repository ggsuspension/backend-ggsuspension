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
            $this->dispatchJob($customer, $customer->created_at);
        }
    }

    public function updated(Customer $customer): void
    {
        $originalStatus = $customer->getOriginal('status');
        $newStatus = $customer->status;
        $originalHargaService = $customer->getOriginal('harga_service');
        $newHargaService = $customer->harga_service;
        $originalHargaSparepart = $customer->getOriginal('harga_sparepart');
        $newHargaSparepart = $customer->harga_sparepart;

        Log::debug("Customer updated", [
            'customer_id' => $customer->id,
            'original_status' => $originalStatus,
            'new_status' => $newStatus,
            'original_harga_service' => $originalHargaService,
            'new_harga_service' => $newHargaService,
            'original_harga_sparepart' => $originalHargaSparepart,
            'new_harga_sparepart' => $newHargaSparepart,
        ]);

        if (!empty(trim($customer->gerai))) {
            if ($originalStatus !== 'FINISH' && $newStatus === 'FINISH') {
                Log::info("Triggering dispatchJob due to status change to FINISH", [
                    'customer_id' => $customer->id,
                ]);
                $this->dispatchJob($customer, $customer->updated_at);
            } elseif (
                $newStatus === 'FINISH' &&
                ($originalHargaService != $newHargaService || $originalHargaSparepart != $newHargaSparepart)
            ) {
                Log::info("Triggering dispatchJob due to harga_service or harga_sparepart change", [
                    'customer_id' => $customer->id,
                ]);
                $this->dispatchJob($customer, $customer->updated_at);
            }
        }
    }

    protected function dispatchJob(Customer $customer, Carbon $date): void
    {
        Log::debug("Entering dispatchJob", [
            'customer_id' => $customer->id,
            'date' => $date->toDateString(),
        ]);

        $gerai = Gerai::where('name', trim($customer->gerai))->first();
        if ($gerai) {
            Log::info("Dispatching CalculateDailyNetRevenue", [
                'customer_id' => $customer->id,
                'gerai_id' => $gerai->id,
                'date' => $date->toDateString(),
            ]);
            CalculateDailyNetRevenue::dispatchSync(
                $gerai->id,
                $date->toDateString()
            );
        } else {
            Log::warning("Gerai not found", [
                'customer_id' => $customer->id,
                'gerai_name' => $customer->gerai,
            ]);
        }
    }
}
