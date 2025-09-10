<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Models\Seal;
use App\Models\StockRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StockRequestController extends Controller
{
    public function getStockRequestsByGerai(int $geraiId): JsonResponse
    {
        $stockRequests = StockRequest::with(['warehouseSeal.motor'])
            ->where('gerai_id', $geraiId)
            ->get();
        return response()->json(['data' => $stockRequests], 200);
    }

    public function getAllStockRequests(): JsonResponse
    {
        $requests = StockRequest::with(['gerai', 'warehouseSeal.motor'])->get();
        return response()->json(['data' => $requests], 200);
    }

    public function requestSeal(Request $request): JsonResponse
    {
        $dataRequest = $request->all();

        if (empty($dataRequest) || !is_array($dataRequest)) {
            return response()->json(['message' => 'Data permintaan tidak valid.'], 400);
        }

        DB::beginTransaction();
        try {
            $dataToInsert = [];
            $now = Carbon::now();
            $sparepartIds = [];

            foreach ($dataRequest as $index => $item) {
                $validator = Validator::make($item, [
                    'gerai_id' => 'required|exists:gerais,id',
                    'sparepart_id' => 'required|exists:spareparts,id',
                ], [
                    'gerai_id.required' => "Gerai ID pada item ke-" . ($index + 1) . " harus diisi.",
                    'sparepart_id.required' => "Sparepart ID pada item ke-" . ($index + 1) . " harus diisi.",
                ]);

                if ($validator->fails()) {
                    throw ValidationException::withMessages($validator->errors()->toArray());
                }

                $validatedItem = $validator->validated();
                $dataToInsert[] = [
                    'gerai_id' => $validatedItem['gerai_id'],
                    'sparepart_id' => $validatedItem['sparepart_id'],
                    'status' => RequestStatus::PENDING->value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $sparepartIds[] = $validatedItem['sparepart_id'];
            }

            if (!empty($dataToInsert)) {
                StockRequest::insert($dataToInsert);
            }

            DB::commit();

            $newRequests = StockRequest::with(['gerai', 'warehouseSeal.motor'])
                ->where('created_at', '>=', $now)
                ->where('gerai_id', $dataRequest[0]['gerai_id'])
                ->whereIn('sparepart_id', $sparepartIds)
                ->get();

            return response()->json([
                'message' => 'Permintaan stok berhasil dibuat.',
                'data' => $newRequests
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat permintaan stok: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    public function updateStockRequest(Request $request, int $stockRequestId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'qty_requested' => 'required|integer|min:1'
        ], [
            'qty_requested.required' => 'Jumlah barang harus diisi.',
            'qty_requested.integer' => 'Jumlah barang harus berupa angka.',
            'qty_requested.min' => 'Jumlah barang minimal 1.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $stockRequest = StockRequest::findOrFail($stockRequestId);

            if ($stockRequest->status !== RequestStatus::PENDING) {
                return response()->json([
                    'message' => 'Hanya permintaan dengan status PENDING yang dapat diubah.'
                ], 400);
            }

            $stockRequest->update($validator->validated());

            return response()->json([
                'message' => 'Kuantitas permintaan berhasil diperbarui.',
                'data' => $stockRequest
            ], 200);
        } catch (\Exception $e) {
            Log::error("Gagal update stock request #{$stockRequestId}: " . $e->getMessage());
            return response()->json(['message' => 'Permintaan stok tidak ditemukan atau gagal diperbarui.'], 404);
        }
    }

    public function approveStockRequest(int $stockRequestId): JsonResponse
    {
        DB::beginTransaction();
        try {
            $stockRequest = StockRequest::with('warehouseSeal')->findOrFail($stockRequestId);

            if ($stockRequest->status !== RequestStatus::PENDING) {
                return response()->json(['message' => 'Permintaan sudah diproses atau ditolak.'], 400);
            }

            if (is_null($stockRequest->qty_requested) || $stockRequest->qty_requested <= 0) {
                return response()->json(['message' => 'Kuantitas belum diisi. Harap edit permintaan terlebih dahulu.'], 400);
            }

            $warehouseSeal = $stockRequest->warehouseSeal;
            if ($warehouseSeal->qty < $stockRequest->qty_requested) {
                return response()->json(['message' => 'Stok di gudang tidak mencukupi.'], 400);
            }

            $warehouseSeal->decrement('qty', $stockRequest->qty_requested);

            Seal::updateOrCreate(
                [
                    'gerai_id' => $stockRequest->gerai_id,
                    'sparepart_id' => $stockRequest->sparepart_id
                ],
                [
                    'category' => $warehouseSeal->category,
                    'name' => $warehouseSeal->name,
                    'motor_id' => $warehouseSeal->motor_id,
                    'price' => $warehouseSeal->price,
                    'purchase_price' => $warehouseSeal->purchase_price,
                    'qty' => DB::raw('qty + ' . $stockRequest->qty_requested),
                ]
            );

            $stockRequest->status = RequestStatus::APPROVED;
            $stockRequest->approved_at = now();
            $stockRequest->save();

            DB::commit();

            return response()->json([
                'message' => 'Permintaan stok disetujui dan stok gerai telah diperbarui.',
                'data' => $stockRequest
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyetujui permintaan stok: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    public function rejectStockRequest(StockRequest $stockRequest): JsonResponse
    {
        if ($stockRequest->status !== RequestStatus::PENDING) {
            return response()->json(['message' => 'Hanya permintaan dengan status PENDING yang dapat ditolak.'], 400);
        }

        try {
            $stockRequest->update([
                'status' => RequestStatus::REJECTED,
                'rejected_at' => now(),
            ]);

            return response()->json([
                'message' => 'Permintaan stok berhasil ditolak.',
                'data' => $stockRequest
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal menolak permintaan stok: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
