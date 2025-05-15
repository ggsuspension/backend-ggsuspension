<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MotorController extends Controller
{
    public function getAllMotors(): JsonResponse
    {
        $motors = Motor::with(['seals', 'motorParts'])->get();
        return response()->json($motors);
    }

    public function getMotorById(Motor $motor): JsonResponse
    {
        return response()->json($motor->load(['seals', 'motorParts']));
    }

    public function createMotor(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:motors',
        ]);

        $motor = Motor::create($validated);
        return response()->json($motor, 201);
    }

    public function updateMotor(Request $request, Motor $motor): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|unique:motors,name,' . $motor->id,
        ]);

        $motor->update($validated);
        return response()->json($motor);
    }

    public function deleteMotor(Motor $motor): JsonResponse
    {
        $motor->delete();
        return response()->json(null, 204);
    }
}
