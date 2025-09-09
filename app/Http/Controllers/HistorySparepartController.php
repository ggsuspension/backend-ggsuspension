<?php

namespace App\Http\Controllers;

use App\Models\HistorySparepart;
use Illuminate\Http\Request;

class HistorySparepartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // HistorySparepart::with();
        // return response()->json(['message' => 'Data berhasil diperoleh', 'data' => HistorySparepart::all()], 200);
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
    public function show(HistorySparepart $historySparepart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HistorySparepart $historySparepart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HistorySparepart $historySparepart)
    {
        //
    }
}
