<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServicePackageResource;
use App\Models\KomstirPricing;
use App\Models\Motor;
use App\Models\ServicePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServicePackageController extends Controller
{
    /**
     * Menampilkan daftar semua service package.
     */
    public function index()
    {
        // Eager load relasi motorType untuk efisiensi query
        $packages = ServicePackage::with(['motorType', 'motors'])->latest()->paginate(10);
        return ServicePackageResource::collection($packages);
    }

    /**
     * Menyimpan service package baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input, termasuk motor_ids yang opsional
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type_service' => 'required|string|max:255',
            'total_price' => 'required|integer',
            'motor_type_id' => 'required|exists:motor_types,id',
            'details' => 'required|array',
            'details.*.category_id' => 'required|exists:categories,id',
            'details.*.motor_part_id' => 'nullable|exists:motor_parts,id',
            'details.*.price' => 'required|integer',
            'details.*.warranty' => 'nullable|string',
            'motor_ids' => 'nullable|array',
            'motor_ids.*' => 'exists:motors,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Gunakan transaction untuk memastikan semua data tersimpan dengan aman
        $package = DB::transaction(function () use ($request) {
            $package = ServicePackage::create([
                'name' => $request->name,
                'type_service' => $request->type_service,
                'total_price' => $request->total_price,
                'motor_type_id' => $request->motor_type_id,
            ]);

            // Simpan detail paket (layanan & part)
            $package->details()->createMany($request->details);

            // Jika ada motor_ids yang dikirim, simpan relasinya
            if ($request->has('motor_ids') && !empty($request->motor_ids)) {
                $package->motors()->attach($request->motor_ids);
            }

            return $package;
        });

        // Load semua relasi yang dibutuhkan sebelum dikirim sebagai response
        $package->load('motorType', 'details.category', 'details.motorPart', 'motors');

        return new ServicePackageResource($package);
    }

    /**
     * Menampilkan satu service package secara spesifik.
     */
    public function show(ServicePackage $servicePackage)
    {
        // Eager load relasi-relasi yang dibutuhkan untuk output JSON
        $servicePackage->load('motorType', 'details.category', 'details.motorPart', 'motors');

        return new ServicePackageResource($servicePackage);
    }

    /**
     * Update service package yang ada di database.
     */
    public function update(Request $request, ServicePackage $servicePackage)
    {
        // Validasi input, sama seperti metode store
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type_service' => 'required|string|max:255',
            'total_price' => 'required|integer',
            'motor_type_id' => 'required|exists:motor_types,id',
            'details' => 'required|array',
            'details.*.category_id' => 'required|exists:categories,id',
            'details.*.motor_part_id' => 'nullable|exists:motor_parts,id',
            'details.*.price' => 'required|integer',
            'details.*.warranty' => 'nullable|string',
            'motor_ids' => 'nullable|array',
            'motor_ids.*' => 'exists:motors,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Gunakan transaction untuk menjaga integritas data saat update
        $package = DB::transaction(function () use ($request, $servicePackage) {
            // Update data utama pada tabel service_packages
            $servicePackage->update([
                'name' => $request->name,
                'type_service' => $request->type_service,
                'total_price' => $request->total_price,
                'motor_type_id' => $request->motor_type_id,
            ]);

            // Hapus semua detail lama dan buat ulang dengan data baru
            $servicePackage->details()->delete();
            $servicePackage->details()->createMany($request->details);

            // Sinkronisasi relasi motor spesifik. `sync` akan menangani penambahan & penghapusan.
            // Jika `motor_ids` kosong atau tidak ada, semua relasi akan dihapus.
            $servicePackage->motors()->sync($request->motor_ids ?? []);

            return $servicePackage;
        });

        // Load relasi yang dibutuhkan sebelum dikirim sebagai response
        $package->load('motorType', 'details.category', 'details.motorPart', 'motors');

        return new ServicePackageResource($package);
    }

    /**
     * Hapus service package dari database.
     */
    public function destroy(ServicePackage $servicePackage)
    {
        // Relasi di tabel pivot akan terhapus otomatis karena onDelete('cascade')
        $servicePackage->delete();
        return response()->json(null, 204); // 204 No Content
    }

    /**
     * Method BARU: Mengambil paket layanan yang relevan untuk motor tertentu.
     */
    public function getPackagesForMotor(Motor $motor)
    {
        // 1. Ambil paket SUSPENSI yang cocok dengan TIPE MOTOR
        $suspensionPackages = ServicePackage::where('motor_type_id', $motor->motor_type_id)
            ->where('type_service', 'SUSPENSI')
            ->get();

        // 2. Ambil paket KOMSTIR yang cocok dengan TIPE MOTOR dari tabel ServicePackage
        $komstirPackagesFromServicePackages = ServicePackage::where('motor_type_id', $motor->motor_type_id)
            ->where('type_service', 'KOMSTIR')
            ->get();

        // 3. Ambil harga KOMSTIR yang cocok dengan MOTOR SPESIFIK dari tabel KomstirPricing
        $komstirPricings = KomstirPricing::where('motor_id', $motor->id)->get();

        // 4. Ubah (transformasi) data harga komstir agar formatnya seragam seperti PaketLayanan
        $komstirServicesFromPricings = $komstirPricings->map(function ($price) {
            return [
                'id' => 'komstir_' . $price->id, // Buat ID unik agar tidak bentrok
                'name' => $price->name . ($price->part_type ? ' (' . $price->part_type . ')' : ''),
                'type_service' => 'KOMSTIR',
                'total_price' => $price->price,
            ];
        });

        // 5. Gabungkan semua koleksi data
        $allServices = $suspensionPackages
            ->toBase()
            ->merge($komstirPackagesFromServicePackages)
            ->merge($komstirServicesFromPricings);

        return response()->json(['data' => $allServices]);
    }
}
