<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Models\HistorySparepart;
use App\Models\Seal;
use App\Models\Sparepart;
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

        DB::beginTransaction();
        // Validasi basic structure
        if (empty($dataRequest) || !is_array($dataRequest)) {
            throw new \Exception('Data tidak valid');
        }
        // Validate each item
        $validated = [];
        foreach ($dataRequest as $index => $item) {
            $itemValidator = Validator::make($item, [
                'gerai_id' => 'required|exists:gerais,id',
                'sparepart_id' => 'required|exists:spareparts,id',
                'qty_requested' => 'required|integer|min:1',
            ], [
                'gerai_id.required' => "Gerai ID pada item ke-" . ($index + 1) . " harus diisi.",
                'gerai_id.exists' => "Gerai pada item ke-" . ($index + 1) . " tidak ditemukan.",
                'sparepart_id.required' => "Sparepart ID pada item ke-" . ($index + 1) . " harus diisi.",
                'sparepart_id.exists' => "Sparepart pada item ke-" . ($index + 1) . " tidak ditemukan.",
                'qty_requested.required' => "Jumlah pada item ke-" . ($index + 1) . " harus diisi.",
                'qty_requested.integer' => "Jumlah pada item ke-" . ($index + 1) . " harus berupa angka.",
                'qty_requested.min' => "Jumlah pada item ke-" . ($index + 1) . " minimal 1.",
            ]);

            if ($itemValidator->fails()) {
                throw ValidationException::withMessages($itemValidator->errors()->toArray());
            }

            $validated[] = $itemValidator->validated();
        }
        $dataToInsert = [];
        foreach ($validated as $item) {
            $dataToInsert[] = array_merge($item, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        StockRequest::insert($dataToInsert);
        DB::commit();
        return response()->json(['message' => 'Data berhasil disimpan']);

        // try {
        //     DB::beginTransaction();
        //     $sparepart = Sparepart::findOrFail($validated['sparepart_id']);
        //     if ($sparepart->qty < $validated['qty_requested']) {
        //         return response()->json([
        //             'error' => 'Stok tidak cukup',
        //             'message' => 'Stok di gudang tidak mencukupi untuk permintaan ini'
        //         ], 400);
        //     }

        //     $stockRequest = StockRequest::create([
        //         'gerai_id' => $validated['gerai_id'],
        //         'sparepart_id' => $validated['sparepart_id'],
        //         'qty_requested' => $validated['qty_requested'],
        //         'status' => RequestStatus::PENDING,
        //     ]);

        //     DB::commit();

        //     Log::info('Stock request created successfully', [
        //         'stock_request_id' => $stockRequest->id,
        //         'gerai_id' => $validated['gerai_id'],
        //         'qty_requested' => $validated['qty_requested'],
        //         'created_at' => $stockRequest->created_at->toISOString(),
        //     ]);

        //     return response()->json([
        //         'data' => $stockRequest,
        //         'message' => 'Permintaan stok berhasil dibuat'
        //     ], 201);
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     Log::error('Failed to create stock request', [
        //         'error' => $e->getMessage(),
        //         'trace' => $e->getTraceAsString(),
        //         'input' => $validated
        //     ]);
        //     return response()->json([
        //         'error' => 'Kesalahan server',
        //         'message' => 'Gagal membuat permintaan stok'
        //     ], 500);
        // }
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
            $sparepartData = Seal::where(['gerai_id' => $stockRequest->gerai_id, 'sparepart_id' => $stockRequest->sparepart_id])->first();
            if (gettype($sparepartData)!="NULL") {
                $sparepartApproved = Seal::where(['gerai_id' => $stockRequest->gerai_id, 'sparepart_id' => $stockRequest->sparepart_id])->first()->update(['qty' => $sparepartData['qty'] + $stockRequest->qty_requested]);    
                Log::info("Seal updated successfully", ['spareparts' => $sparepartApproved]); 
                DB::commit();  
                return response()->json([
                    'message' => 'Permintaan stok disetujui dan Seal telah disimpan',
                    'data' => $sparepartApproved
                ], 200);
            }
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
            return response()->json([
                'message' => 'Permintaan stok disetujui dan Seal telah disimpan',
                'data' => $sparepartApproved,
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
