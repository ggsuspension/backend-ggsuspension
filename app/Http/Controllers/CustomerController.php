<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Customer::all();
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
        $data = Customer::create($request->all());
        return response()->json(['success' => true, 'data' => $data], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'nama' => 'sometimes',
            'plat_motor' => 'sometimes',
            'noWA' => 'sometimes',
            'gerai' => 'sometimes',
            'sudah_chat' => 'sometimes',
            'sumber_info' => 'sometimes',
            'status' => 'sometimes',
            'sparepart' => 'sometimes',
            'sparepart_id' => 'sometimes',
            'layanan' => 'sometimes',
            'jenis_motor' => 'sometimes',
            'harga_service' => 'sometimes',
            'harga_sparepart' => 'sometimes',
            'bagian_motor' => 'sometimes',
            'bagian_motor2' => 'sometimes',
            'motor' => 'sometimes',
        ]);
        $customer->update($validated);
        return response()->json(['success' => true, 'data' => $customer], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
