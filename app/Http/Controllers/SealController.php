<?php

namespace App\Http\Controllers;

use App\Models\Seal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SealController extends Controller
{
    public function getAllSeals(): JsonResponse
    {
        $seals = Seal::with(['motor', 'gerai'])->get();
        return response()->json($seals);
    }

    public function getSeal(Seal $seal): JsonResponse
    {
        return response()->json($seal->load(['motor', 'gerai']));
    }

    public function getSealsByGerai(int $geraiId): JsonResponse
    {
        $seals = Seal::where('gerai_id', $geraiId)->with(['motor'])->get();
        return response()->json($seals);
    }

    public function getSealsByGeraiId(int $geraiId): JsonResponse
    {
        $seals = Seal::where('gerai_id', $geraiId)->get();
        return response()->json($seals);
    }

    public function updateSeal(Request $request, Seal $seal): JsonResponse
    {
        $validated = $request->validate([
            'qty' => 'integer',
        ]);
        $data = Seal::where('sparepart_id', $seal->id)->update($validated);
        // $seal->update($validated);
        return response()->json($request->all());
    }

    public function deleteSeal(Seal $seal): JsonResponse
    {
        $seal->delete();
        return response()->json(null, 204);
    }
}
