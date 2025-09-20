<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AntrianController extends Controller
{
    public function getAntrianByGeraiId(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'gerai_id' => 'required|exists:gerais,id',
                'date' => 'nullable|date_format:d-m-y',
            ]);
            $geraiId = $validated['gerai_id'];
            $startDate = isset($validated['date'])
                ? Carbon::createFromFormat('d-m-y', $validated['date'])->startOfDay()
                : Carbon::today()->startOfDay();
            $endDate = $startDate->copy()->endOfDay();

            $query = Order::withoutGlobalScopes()
                ->where('gerai_id', $geraiId)
                ->whereBetween('waktu', [$startDate, $endDate])
                ->with([
                    'gerai',
                    'motor',
                    'motorPart.subcategory.category',
                    'seals',
                    'customer',
                ]);

            if (!isset($validated['date'])) {
                $query->whereNotIn('status', [OrderStatus::FINISHED, OrderStatus::CANCELLED]);
            }

            $orders = $query->get();
            $formattedOrders = $orders->map(fn($order) => $this->formatOrderResponse($order));

            return response()->json([
                'message' => 'Daftar antrian berhasil diambil',
                'data' => $formattedOrders,
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validasi gagal untuk getAntrian', ['errors' => $e->errors()]);
            return response()->json(['error' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil antrian: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Gagal mengambil antrian'], 500);
        }
    }

    public function getAntrianAllOutlet()
    {
        try {
            $query = Order::withoutGlobalScopes()
                ->with([
                    'gerai',
                    'motor',
                    'motorPart.subcategory.category',
                    'seals',
                    'customer',
                ]);

            $orders = $query->get();
            $formattedOrders = $orders->map(fn($order) => $this->formatOrderResponse($order));

            return response()->json([
                'message' => 'Daftar antrian berhasil diambil',
                'data' => $formattedOrders,
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validasi gagal untuk getAntrian', ['errors' => $e->errors()]);
            return response()->json(['error' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil antrian: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Gagal mengambil antrian'], 500);
        }
    }

    public function getAntrianSemuaGerai()
    {
        try {
            $customers = Customer::all();
            return response()->json(['data' => $customers]);
        } catch (ValidationException $e) {
        }
    }


    private function formatOrderResponse(Order $order): array
    {
        try {
            Log::debug('Memformat response untuk order ID: ' . $order->id, [
                'waktu' => $order->waktu,
                'gerai_id' => $order->gerai_id,
                'status' => $order->status ? $order->status->value : 'UNKNOWN',
                'plat' => $order->plat,
                'customer_exists' => !is_null($order->customer),
                'customer_data' => $order->customer ? $order->customer->toArray() : null,
            ]);

            return [
                'id' => $order->id,
                'nama' => $order->customer ? $order->customer->nama : $order->nama,
                'plat' => $order->customer ? $order->customer->plat : $order->plat,
                'noWA' => $order->customer ? $order->customer->no_wa : $order->no_wa,
                'waktu' => $order->waktu->toIso8601String(),
                'geraiId' => $order->gerai_id,
                'totalHarga' => $order->total_harga,
                'status' => $order->status->value,
                'motorId' => $order->motor_id,
                'motorPartId' => $order->motor_part_id,
                'createdAt' => $order->created_at->toIso8601String(),
                'updatedAt' => $order->updated_at->toIso8601String(),
                'gerai' => $order->gerai ? [
                    'id' => $order->gerai->id,
                    'name' => $order->gerai->name,
                ] : null,
                'motor' => $order->motor ? [
                    'id' => $order->motor->id,
                    'name' => $order->motor->name,
                ] : null,
                'motorPart' => $order->motorPart ? [
                    'id' => $order->motorPart->id,
                    'service' => $order->motorPart->service,
                    'price' => $order->motorPart->price,
                    'subcategoryId' => $order->motorPart->subcategory_id,
                    'subcategory' => $order->motorPart->subcategory ? [
                        'id' => $order->motorPart->subcategory->id,
                        'name' => $order->motorPart->subcategory->name,
                        'categoryId' => $order->motorPart->subcategory->category_id,
                        'category' => $order->motorPart->subcategory->category ? [
                            'id' => $order->motorPart->subcategory->category->id,
                            'name' => $order->motorPart->subcategory->category->name,
                        ] : null,
                    ] : null,
                ] : null,
                'seals' => $order->seals->map(function ($seal) {
                    return [
                        'orderId' => $seal->pivot->order_id,
                        'sealId' => $seal->pivot->seal_id,
                        'seal' => [
                            'id' => $seal->id,
                            'ccRange' => $seal->cc_range,
                            'price' => $seal->price,
                            'qty' => $seal->qty,
                        ],
                    ];
                })->toArray(),
            ];
        } catch (\Exception $e) {
            Log::error('Gagal memformat response untuk order ID: ' . $order->id . ': ' . $e->getMessage(), ['exception' => $e]);
            return [];
        }
    }


    public function updateAntrian($id, Request $request): JsonResponse
    {
        try {
            $order = Customer::find($id);
            if (!$order) {
                return response()->json(['error' => 'Order tidak ditemukan'], 404);
            }
            $order->update($request->all());

            return response()->json([
                'message' => 'Order berhasil diperbarui',
            ]);
        } catch (ValidationException $e) {
            Log::error('Validasi request gagal untuk update order ID: ' . $id, [
                'errors' => $e->errors(),
            ]);
            return response()->json(['error' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui order: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Gagal memperbarui order: ' . $e->getMessage()], 500);
        }
    }

    public function finishOrder($id): JsonResponse
    {
        try {
            Customer::find($id)->update(['status' => "FINISH"]);
            return response()->json([
                'message' => 'Motor selesai dikerjakan',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyelesaikan order: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Gagal menyelesaikan order: ' . $e->getMessage()], 500);
        }
    }

    public function cancelOrder($id, Request $request): JsonResponse
    {
        try {
            Customer::find($id)->update([
                'status' => "CANCEL",
                'data_lainnya' => $request->all()
            ]);
            return response()->json([
                'message' => 'Pengerjaan berhasil dibatalkan dan stok seal dikembalikan',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membatalkan order: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Gagal membatalkan order: ' . $e->getMessage()], 500);
        }
    }

    private function validateOrder(Order $order): bool
    {
        $isValid = !is_null($order->waktu) &&
            !is_null($order->gerai_id) &&
            !is_null($order->motor_id) &&
            !is_null($order->motor_part_id) &&
            !is_null($order->status) &&
            !is_null($order->plat);

        Log::debug('Validasi order ID: ' . $order->id, [
            'waktu' => $order->waktu,
            'gerai_id' => $order->gerai_id,
            'motor_id' => $order->motor_id,
            'motor_part_id' => $order->motor_part_id,
            'status' => $order->status,
            'plat' => $order->plat,
            'isValid' => $isValid,
        ]);

        return $isValid;
    }
}
