<?php

namespace App\Http\Controllers;

use App\Models\KomstirPricing;
use App\Models\Motor;
use Illuminate\Http\Request;

class KomstirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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

    public function getPricesByMotor(Motor $motor)
    {
        // Cari harga berdasarkan motor_id yang diberikan
        $prices = KomstirPricing::where('motor_id', $motor->id)->get();
        return response()->json($prices);
    }
}
