<?php

namespace App\Http\Controllers;

use App\Models\CustomerMotor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerMotorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $motors = CustomerMotor::all();
        return response()->json($motors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_profile_id' => 'required|exists:customer_profiles,id',
            'nama_motor' => 'required|string|max:255',
            'jenis_motor' => 'required|string|max:100',
            'plat_motor' => 'required|string|max:15|unique:customer_motors,plat_motor',
        ]);

        $validated['plat_motor'] = strtoupper(str_replace(' ', '', $validated['plat_motor']));

        $motor = CustomerMotor::create($validated);

        return response()->json($motor, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $motor = CustomerMotor::find($id);

        if (!$motor) {
            return response()->json(['message' => 'Motor not found'], 404);
        }

        return response()->json($motor);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $motor = CustomerMotor::find($id);

        if (!$motor) {
            return response()->json(['message' => 'Motor not found'], 404);
        }

        $validated = $request->validate([
            'customer_profile_id' => 'sometimes|required|exists:customer_profiles,id',
            'nama_motor' => 'sometimes|required|string|max:255',
            'jenis_motor' => 'sometimes|required|string|max:100',
            'plat_motor' => [
                'sometimes',
                'required',
                'string',
                'max:15',
                Rule::unique('customer_motors', 'plat_motor')->ignore($motor->id),
            ],
        ]);

        if (isset($validated['plat_motor'])) {
            $validated['plat_motor'] = strtoupper(str_replace(' ', '', $validated['plat_motor']));
        }

        $motor->update($validated);

        return response()->json($motor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $motor = CustomerMotor::find($id);

        if (!$motor) {
            return response()->json(['message' => 'Motor not found'], 404);
        }

        $motor->delete();

        return response()->json(['message' => 'Motor successfully deleted'], 200);
    }
}
