<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WarehouseSealController extends Controller
{
    public function index(): JsonResponse
    {
        $warehouseSeals = Sparepart::with('motor')->get();
        return response()->json(['data' => $warehouseSeals], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
            'qty' => 'required|integer|min:0',
            'purchase_price' => 'required|integer|min:0',
            "category" => 'required|string',
            'motor_id' => 'sometimes',
        ]);

        $warehouseSeal = Sparepart::create($validated);
        $user = Auth::guard('api')->user();
        Log::info('Sparepart created', [
            'id' => $warehouseSeal->id,
            'user_id' => $user ? $user->id : null,
        ]);
        return response()->json("BERHASIL", 201);
    }

    public function show($id): JsonResponse
    {
        $warehouseSeal = Sparepart::with('motor')->findOrFail($id);
        return response()->json(['data' => $warehouseSeal], 200);
    }


    public function update(Request $request, $id): JsonResponse
    {
        $warehouseSeal = Sparepart::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|integer|min:0',
            'qty' => 'sometimes|integer|min:0',
            'purchase_price' => 'sometimes|integer|min:0',
            "category" => 'sometimes|string',
            'motor_id' => 'sometimes',
        ]);

        $warehouseSeal->update($validated);
        return response()->json(['data' => $warehouseSeal->load('motor')], 200);
    }


    public function destroy($id): JsonResponse
    {
        $warehouseSeal = Sparepart::findOrFail($id);
        $warehouseSeal->delete();
        return response()->json(['message' => 'Warehouse seal berhasil dihapus'], 200);
    }
}
