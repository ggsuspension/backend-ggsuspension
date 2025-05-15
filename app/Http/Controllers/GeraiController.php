<?php

namespace App\Http\Controllers;

use App\Models\Gerai;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeraiController extends Controller
{
    public function getAllGerais(): JsonResponse
    {
        $gerais = Gerai::with(['users', 'orders', 'seals'])->get();
        return response()->json($gerais);
    }

    public function getGeraiById(Gerai $gerai): JsonResponse
    {
        return response()->json($gerai->load(['users', 'orders', 'seals']));
    }

    public function createGerai(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:gerais',
            'location' => 'required|string',
        ]);

        $gerai = Gerai::create($validated);
        return response()->json($gerai, 201);
    }

    public function updateGerai(Request $request, Gerai $gerai): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|unique:gerais,name,' . $gerai->id,
            'location' => 'string',
        ]);

        $gerai->update($validated);
        return response()->json($gerai);
    }

    public function deleteGerai(Gerai $gerai): JsonResponse
    {
        $gerai->delete();
        return response()->json(null, 204);
    }
}
