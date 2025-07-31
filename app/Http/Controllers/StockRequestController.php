<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Models\Seal;
use App\Models\Sparepart;
use App\Models\StockRequest;
use App\Models\WarehouseSeal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        Log::info('Stock requests fetched: ' . json_encode($requests));
        return response()->json(['data' => $requests], 200);
    }

    public function requestSeal(Request $request): JsonResponse
    {
        Log::info('Request data received:', $request->all());

        $validated = $request->validate([
            'gerai_id' => 'required|exists:gerais,id',
            'sparepart_id' => 'required|exists:spareparts,id',
            'qty_requested' => 'required|integer|min:1',
        ], [
            'gerai_id.required' => 'Gerai ID harus diisi.',
            'gerai_id.exists' => 'Gerai dengan ID tersebut tidak ditemukan.',
            'sparepart_id.required' => 'Sparepart ID harus diisi.',
            'sparepart_id.exists' => 'Sparepart dengan ID tersebut tidak ditemukan.',
            'qty_requested.required' => 'Jumlah yang diminta harus diisi.',
            'qty_requested.integer' => 'Jumlah yang diminta harus berupa angka bulat.',
            'qty_requested.min' => 'Jumlah yang diminta minimal 1.'
        ]);

        try {
            DB::beginTransaction();
            $sparepart = Sparepart::findOrFail($validated['sparepart_id']);
            Log::info('Stock check', [
                'sparepart_id' => $sparepart->id,
                'qty' => $sparepart->qty,
                'qty_requested' => $validated['qty_requested'],
                'comparison' => $sparepart->qty < $validated['qty_requested']
            ]);

            if ($sparepart->qty < $validated['qty_requested']) {
                return response()->json([
                    'error' => 'Stok tidak cukup',
                    'message' => 'Stok di gudang tidak mencukupi untuk permintaan ini'
                ], 400);
            }

            $stockRequest = StockRequest::create([
                'gerai_id' => $validated['gerai_id'],
                'sparepart_id' => $validated['sparepart_id'],
                'qty_requested' => $validated['qty_requested'],
                'status' => RequestStatus::PENDING,
            ]);

            DB::commit();

            Log::info('Stock request created successfully', [
                'stock_request_id' => $stockRequest->id,
                'gerai_id' => $validated['gerai_id'],
                'qty_requested' => $validated['qty_requested'],
                'created_at' => $stockRequest->created_at->toISOString(),
            ]);

            return response()->json([
                'data' => $stockRequest,
                'message' => 'Permintaan stok berhasil dibuat'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create stock request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $validated
            ]);
            return response()->json([
                'error' => 'Kesalahan server',
                'message' => 'Gagal membuat permintaan stok'
            ], 500);
        }
    }

    public function approveStockRequest($stockRequestId): JsonResponse
    {
        try {
            DB::beginTransaction();
            // Ambil StockRequest
            $stockRequest = StockRequest::findOrFail($stockRequestId);
            // Periksa apakah status permintaan adalah PENDING
            if ($stockRequest->status != RequestStatus::PENDING) {
                return response()->json([
                    'error' => 'Permintaan stok tidak dapat disetujui',
                    'message' => 'Permintaan sudah diproses atau ditolak'
                ], 400);
            }
            $warehouseSeal = $stockRequest->warehouseSeal;

            if ($warehouseSeal->qty < $stockRequest->qty_requested) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Stok tidak cukup',
                    'message' => 'Stok di gudang tidak mencukupi untuk permintaan ini'
                ], 400);
            }
            $warehouseSeal->qty -= $stockRequest->qty_requested;
            $warehouseSeal->save();

            // Update status StockRequest menjadi APPROVED
            $stockRequest->status = RequestStatus::APPROVED;
            $stockRequest->approved_at = now();
            $stockRequest->save();

            $sparepartApproved = Seal::create([
                'category' => $warehouseSeal->category,
                'name' => $warehouseSeal->name,
                'motor_id' => $warehouseSeal->motor_id,
                'price' => $warehouseSeal->price,
                'sparepart_id' => $warehouseSeal->id,
                'qty' => $stockRequest->qty_requested,
                'gerai_id' => $stockRequest->gerai_id,
            ]);
            DB::commit();

            Log::info('Stock request approved successfully', [
                'stock_request_id' => $stockRequest->id,
                'warehouse_seal_id' => $warehouseSeal->id,
                'qty_requested' => $stockRequest->qty_requested,
                'remaining_warehouse_qty' => $warehouseSeal->qty,
            ]);

            return response()->json([
                'message' => 'Permintaan stok disetujui dan Seal telah disimpan',
                'data' => $sparepartApproved
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve stock request', [
                'stock_request_id' => $stockRequestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'Kesalahan server',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function rejectStockRequest(StockRequest $stockRequest): JsonResponse
    {
        if (!$stockRequest->exists) {
            Log::warning('Stock request tidak ditemukan', [
                'stock_request_id_param' => request()->route('stockRequest')
            ]);
            return response()->json([
                'error' => 'Stock request tidak ditemukan',
                'message' => 'Permintaan stok yang dimaksud tidak ada'
            ], 404);
        }

        Log::info('Debug status stock request', [
            'stock_request_id' => $stockRequest->id,
            'status' => $stockRequest->status,
            'status_type' => gettype($stockRequest->status),
            'status_value' => $stockRequest->status instanceof RequestStatus ? $stockRequest->status->value : 'bukan enum',
            'pending_value' => RequestStatus::PENDING->value,
            'is_pending' => $stockRequest->status === RequestStatus::PENDING
        ]);

        if ($stockRequest->status !== RequestStatus::PENDING) {
            return response()->json([
                'error' => 'Stock request tidak bisa ditolak',
                'message' => 'Permintaan harus dalam status PENDING'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $stockRequest->update([
                'status' => RequestStatus::REJECTED,
                'rejected_at' => now(),
            ]);

            DB::commit();

            $stockRequest->load(['warehouseSeal', 'gerai']);

            Log::info('Stock request berhasil ditolak', [
                'stock_request_id' => $stockRequest->id,
                'gerai_id' => $stockRequest->gerai->id ?? null
            ]);

            return response()->json([
                'data' => $stockRequest,
                'message' => 'Stock request berhasil ditolak'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal menolak stock request', [
                'stock_request_id' => $stockRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Kesalahan server',
                'message' => 'Gagal memproses penolakan stock request'
            ], 500);
        }
    }
}
