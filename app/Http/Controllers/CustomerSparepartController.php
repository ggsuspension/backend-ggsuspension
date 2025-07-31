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
        $today = Carbon::today();
        $data = CustomerSparepart::whereDate('created_at', $today)->with(['sparepart', 'gerai'])->get()->map(function ($customerSparepart) {
            return [
                'id' => $customerSparepart->id,
                'gerai_id' => $customerSparepart->gerai_id,
                'gerai_name' => $customerSparepart->gerai->name,
                'sparepart_name' => $customerSparepart->sparepart->category." - ".$customerSparepart->sparepart->name,
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
