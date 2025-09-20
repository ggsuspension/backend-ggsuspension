<?php

namespace App\Http\Controllers;

use App\Http\Resources\MotorPartResource;
use App\Models\MotorPart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class MotorPartController extends Controller
{
    public function getAllMotorParts(): JsonResponse
    {
        $motorParts = MotorPart::with(['motorType', 'categories'])->get();
        return response()->json($motorParts);
    }

    public function getMotorPartById(MotorPart $motorPart): JsonResponse
    {
        $motorPart->load(['motorType', 'motors']);
        return response()->json(new MotorPartResource($motorPart));
    }

    public function createMotorPart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Menambahkan validasi unique composite key sesuai skema database
                Rule::unique('motor_parts')->where(function ($query) use ($request) {
                    return $query->where('motor_type_id', $request->motor_type_id)
                        ->where('cc_range', $request->cc_range);
                }),
            ],
            'price' => 'required|integer|min:0',
            'cc_range' => 'nullable|string|max:255',
            'motor_type_id' => 'required|exists:motor_types,id',
        ]);

        $motorPart = MotorPart::create($validated);
        return response()->json($motorPart, 201);
    }

    public function updateMotorPart(Request $request, MotorPart $motorPart): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('motor_parts')->ignore($motorPart->id)->where(function ($query) use ($request) {
                    return $query->where('motor_type_id', $request->motor_type_id)
                        ->where('cc_range', $request->cc_range);
                }),
            ],
            'price' => 'sometimes|required|integer|min:0',
            'cc_range' => 'sometimes|nullable|string|max:255',
            'motor_type_id' => 'sometimes|required|exists:motor_types,id',
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
