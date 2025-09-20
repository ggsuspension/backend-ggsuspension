<?php

namespace App\Http\Controllers;

use App\Models\CustomerMotor;
use App\Models\CustomerProfile;
use App\Models\Motor;
use App\Models\MotorType;
use App\Models\ServicePackage;
use App\Models\ServiceQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceQueueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $queues = ServiceQueue::where('status', '!=', 'Completed')->latest()->get();
        return response()->json($queues);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // 1. Validasi input dari frontend
            $validated = $request->validate([
                'customer_profile_id' => 'required|exists:customer_profiles,id',
                'customer_motor_id'   => 'required|exists:customer_motors,id',
                'selected_package_ids' => 'required|array|min:1',
                'selected_package_ids.*' => 'required|integer|exists:service_packages,id',
            ]);

            // 2. Siapkan struktur dasar JSON
            $servicesJson = [
                'suspensi' => null,
                'komstir' => null,
            ];

            // 3. Ambil data ServicePackage dengan eager loading ganda
            $packages = ServicePackage::with(['details.category', 'details.motorPart'])->findMany($validated['selected_package_ids']);

            // Inisialisasi variabel untuk menentukan tipe layanan gabungan
            $hasSuspensi = false;
            $hasKomstir = false;

            foreach ($packages as $package) {
                $group = strtolower($package->type_service);

                // Set flag untuk tipe layanan yang ada
                if ($group === 'suspensi') {
                    $hasSuspensi = true;
                }
                if ($group === 'komstir') {
                    $hasKomstir = true;
                }

                if (is_null($servicesJson[$group])) {
                    $servicesJson[$group] = [
                        'status' => 'Menunggu',
                        'klaim_garansi_expire' => null,
                        'layanan_details' => [],
                    ];
                }

                foreach ($package->details as $detail) {
                    $layananDetail = [
                        'layanan'           => $detail->category->name ?? null,
                        'part_motor'        => $detail->motorPart->name ?? null,
                        'cc_range'          => $detail->cc_range,
                        'warranty'          => $detail->warranty,
                        'price'             => $detail->price,
                        'sparepart_include' => $detail->sparepart_include ?? [],
                        'used_spareparts' => []
                    ];
                    array_push($servicesJson[$group]['layanan_details'], $layananDetail);
                }
            }

            $finalServicesJson = array_filter($servicesJson, function ($value) {
                return !is_null($value);
            });

            // 4. Tentukan dan set service_type_id berdasarkan tipe layanan yang ada
            $serviceType = '';
            if ($hasSuspensi && $hasKomstir) {
                $serviceType = 'KOMSTIR + SUSPENSI';
            } elseif ($hasSuspensi) {
                $serviceType = 'SUSPENSI';
            } elseif ($hasKomstir) {
                $serviceType = 'KOMSTIR';
            }

            // PERBAIKAN: Dapatkan service_type_id dari MotorType yang sudah ada di database
            // Asumsi: Anda memiliki tabel `motor_types` yang juga menyimpan tipe layanan.
            // Jika tidak, Anda perlu membuat tabel `service_types` dan modelnya.
            $motorType = MotorType::where('name', $serviceType)->first();
            $serviceTypeId = $motorType->id ?? null;


            // 5. Buat entri baru di tabel service_queues
            $serviceQueue = ServiceQueue::create([
                'customer_profile_id' => $validated['customer_profile_id'],
                'customer_motor_id'   => $validated['customer_motor_id'],
                'services'            => $finalServicesJson,
                'status'              => 'Waiting',
                'service_type_id'     => $serviceTypeId,
            ]);

            DB::commit();
            return response()->json($serviceQueue, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat antrian servis: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json(['error' => 'Failed to create service queue: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceQueue $serviceQueue)
    {
        $serviceQueue->load(['customerProfile', 'customerMotor']);
        return response()->json($serviceQueue);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceQueue $serviceQueue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceQueue $serviceQueue)
    {
        // Validasi input
        $validated = $request->validate([
            'services' => 'sometimes|array',
            'status' => 'sometimes|string|in:Waiting,In Progress,Completed',
            'completed_at' => 'sometimes|date|nullable',
        ]);

        // Update data antrian
        $serviceQueue->update($validated);

        return response()->json($serviceQueue);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceQueue $serviceQueue)
    {
        $serviceQueue->delete();
        return response()->json(null, 204); // 204 = No Content
    }
}
