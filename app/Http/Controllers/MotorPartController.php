<?php

namespace App\Http\Controllers;

use App\Http\Resources\MotorPartResource;
use App\Models\MotorPart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MotorPartController extends Controller
{
    public function getAllMotorParts(): JsonResponse
    {
        $motorParts = MotorPart::all();
        return response()->json($motorParts);
    }

    public function getMotorPartById(MotorPart $motorPart): JsonResponse
    {
        $motorPart->load(['subcategory.category', 'motors', 'orders']);
        return response()->json(new MotorPartResource($motorPart));
    }

    public function createMotorPart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service' => 'required|string',
            'price' => 'required|integer',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        $motorPart = MotorPart::create($validated);
        return response()->json($motorPart, 201);
    }

    public function updateMotorPart(Request $request, MotorPart $motorPart): JsonResponse
    {
        $validated = $request->validate([
            'service' => 'string',
            'price' => 'integer',
            'subcategory_id' => 'exists:subcategories,id',
        ]);

        $motorPart->update($validated);
        return response()->json($motorPart);
    }

    public function deleteMotorPart(MotorPart $motorPart): JsonResponse
    {
        $motorPart->delete();
        return response()->json(null, 204);
    }
}
