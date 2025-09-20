<?php

namespace App\Http\Controllers;

use App\Models\CustomerMotor;
use App\Models\CustomerProfile;
use Illuminate\Http\Request;

class CustomerProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = CustomerProfile::with('motors')->get();
        return response()->json($customers);
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
        // Validasi data yang masuk
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'noWA' => 'required|string|max:20|unique:customer_profiles,noWA',
            'sumber_info' => 'nullable|string',
            'sudah_chat' => 'nullable|string',
        ]);

        // Buat data pelanggan baru
        $customer = CustomerProfile::create($validated);

        // Kembalikan response JSON dengan status 201 (Created)
        return response()->json($customer, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerProfile $customerProfile)
    {
        $customerProfile->load('motors');
        return response()->json($customerProfile);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerProfile $customerProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerProfile $customerProfile)
    {
        // Validasi data yang masuk
        // 'noWA' dibuat unik kecuali untuk entri yang sedang diperbarui
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'noWA' => 'required|string|max:20|unique:customer_profiles,noWA,' . $customerProfile->id,
            'sumber_info' => 'nullable|string',
            'sudah_chat' => 'nullable|string',
        ]);

        // Perbarui data pelanggan
        $customerProfile->update($validated);

        // Kembalikan response JSON dengan data yang sudah diperbarui
        return response()->json($customerProfile);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerProfile $customerProfile)
    {
        // Hapus data pelanggan
        $customerProfile->delete();

        // Kembalikan response JSON sukses
        return response()->json(['message' => 'Customer profile successfully deleted'], 200);
    }

    /**
     * Method BARU: Mencari pelanggan berdasarkan nomor WhatsApp.
     */
    public function search(Request $request)
    {
        $request->validate(['noWA' => 'required|string']);

        // Cari pelanggan berdasarkan noWA dan muat relasi motornya
        // Pastikan nama relasi di model CustomerProfile adalah 'motors'
        $customer = CustomerProfile::where('noWA', $request->noWA)
            ->with('motors')
            ->first();

        if ($customer) {
            return response()->json($customer);
        }

        return response()->json(['message' => 'Pelanggan tidak ditemukan.'], 404);
    }

    /**
     * Mencari pelanggan berdasarkan plat motor.
     */
    public function searchByPlat(Request $request)
    {
        $request->validate(['plat_motor' => 'required|string']);

        // Normalisasi input plat motor: hapus spasi dan ubah ke huruf besar
        $platMotorInput = strtoupper(str_replace(' ', '', $request->plat_motor));

        // Cari motor berdasarkan plat_motor yang sudah dinormalisasi
        $motor = CustomerMotor::where('plat_motor', $platMotorInput)->first();

        if (!$motor) {
            return response()->json(['message' => 'Motor tidak ditemukan.'], 404);
        }

        $customer = $motor->customerProfile()->with('motors')->first();

        if ($customer) {
            return response()->json([
                'customer' => $customer,
                'motor' => $motor
            ]);
        }

        return response()->json(['message' => 'Customer tidak ditemukan.'], 404);
    }
}
