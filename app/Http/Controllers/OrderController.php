<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Seal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            Order::create($request->all());

            return response()->json("Berhasil menambah data!", 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed: ' . json_encode($e->errors()), [
                'request' => $request->all(),
            ]);
            return response()->json(
                ['error' => 'Validation failed', 'details' => $e->errors()],
                422
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);
            return response()->json(
                ['error' => 'Failed to create order: ' . $e->getMessage()],
                500
            );
        }
    }
}
