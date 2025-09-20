<?php

namespace App\Http\Controllers;

use App\Models\MotorType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MotorTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $motorTypes = MotorType::orderBy('name')->get(['id', 'name']);
        return response()->json($motorTypes);
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
}
