<?php

namespace App\Http\Controllers;

use App\Models\CustomerSparepart;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerSparepartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data dari tabel pivot CustomerSparepart
        // dengan relasi yang dibutuhkan (sparepart, gerai, customer)
        $data = CustomerSparepart::with(['sparepart', 'gerai', 'customer'])->get()->map(function ($customerSparepart) {

            // PERBAIKAN LOGIKA DI SINI
            // Cek apakah sparepart_id ada dan relasi sparepart tidak null
            if ($customerSparepart->sparepart) {
                // Jika ADA, ini adalah sparepart yang terdaftar di database
                $sparepart_name = $customerSparepart->sparepart->category . " - " . $customerSparepart->sparepart->name;
                $sparepart_price = $customerSparepart->sparepart->price;
            } else {
                // Jika TIDAK ADA (null), ini adalah sparepart offline/manual.
                // Ambil data langsung dari kolom di tabel pivot (customer_sparepart).
                $sparepart_name = $customerSparepart->name; // 'name' dari sparepart offline
                $sparepart_price = $customerSparepart->price; // 'price' dari sparepart offline
            }

            return [
                'id' => $customerSparepart->id,
                'gerai_id' => $customerSparepart->gerai_id,
                'gerai_name' => $customerSparepart->gerai->name,
                'status' => $customerSparepart->customer->status,
                'sparepart_price' => $sparepart_price, // Gunakan harga yang sudah ditentukan
                'sparepart_name' => $sparepart_name, // Gunakan nama yang sudah ditentukan
                'motor' => $customerSparepart->customer->motor,
                'qty' => $customerSparepart->qty,
                'created_at' => $customerSparepart->created_at,
            ];
        });

        return response()->json($data);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
