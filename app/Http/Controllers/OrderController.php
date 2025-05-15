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
            // Validasi request
            // $validated = $request->validate([
            //     'nama' => 'required|string',
            //     'layanan' => 'nullable|string',
            //     'subcategory' => 'nullable|string',
            //     'motor' => 'nullable|string',
            //     'bagian_motor' => 'nullable|string',
            //     'bagian_motor2' => 'nullable|string',
            //     'harga_layanan' => 'nullable|integer',
            // ]);
            DB::beginTransaction();
            Order::create($request->all());
            // if (!empty($validated['seal_ids'])) {
            //     foreach ($validated['seal_ids'] as $sealId) {
            //         // Ambil seal berdasarkan ID dan gerai_id
            //         $seal = Seal::where('id', $sealId)
            //             ->where('gerai_id', $validated['gerai_id'])
            //             ->lockForUpdate() // Cegah race condition
            //             ->first();

            //         if (!$seal) {
            //             throw new \Exception("Seal dengan ID $sealId tidak ditemukan di gerai ini.");
            //         }

            //         if ($seal->qty < 1) {
            //             throw new \Exception("Stok seal dengan ID $sealId tidak mencukupi.");
            //         }

            //         // Kurangi stok seal
            //         $seal->decrement('qty', 1);
            //     }

            //     // Attach seals ke order
            //     $order->seals()->attach($validated['seal_ids']);
            // }

            // // Generate customer ID
            // $customerId = $validated['plat'] . '-' . now()->timestamp;

            // // Buat Customer
            // $customer = Customer::create([
            //     'id' => $customerId,
            //     'nama' => $validated['nama'],
            //     'no_wa' => $validated['no_wa'],
            //     'gerai_id' => $validated['gerai_id'],
            //     'plat' => $validated['plat'],
            //     'layanan' => $validated['layanan'] ?? null,
            //     'subcategory' => $validated['subcategory'] ?? null,
            //     'motor' => $validated['motor'] ?? null,
            //     'bagian_motor' => $validated['bagian_motor'] ?? null,
            //     'harga_layanan' => $validated['harga_layanan'] ?? null,
            //     'harga_seal' => $validated['harga_seal'] ?? null,
            //     'total_harga' => $validated['total_harga'],
            //     'seal' => $validated['seal'] ?? null,
            //     'info' => $validated['info'] ?? null,
            //     'sumber_info' => $validated['sumber_info'] ?? null,
            //     'status' => OrderStatus::PROGRESS,
            //     'warranty_claimed' => false,
            // ]);

            // // Commit transaksi
            // DB::commit();

            // // Load relationships
            // $order->load([
            //     'gerai',
            //     'motor',
            //     'motorPart.subcategory.category',
            //     'seals' => function ($query) {
            //         $query->with('motor');
            //     },
            //     'customer',
            // ]);

            // // Transform response (sama seperti kode asli, tidak diubah)
            // $response = [
            //     'id' => $order->id,
            //     'nama' => $order->nama,
            //     'plat' => $order->plat,
            //     'no_wa' => $order->no_wa,
            //     'waktu' => $order->waktu ? $order->waktu->toISOString() : null,
            //     'gerai_id' => $order->gerai_id,
            //     'total_harga' => $order->total_harga,
            //     'status' => $order->status->value,
            //     'motor_id' => $order->motor_id,
            //     'motor_part_id' => $order->motor_part_id,
            //     'created_at' => $order->created_at ? $order->created_at->toISOString() : null,
            //     'updated_at' => $order->updated_at ? $order->updated_at->toISOString() : null,
            //     'motor' => $order->motor ? [
            //         'id' => $order->motor->id,
            //         'name' => $order->motor->name,
            //         'created_at' => $order->motor->created_at ? $order->motor->created_at->toISOString() : null,
            //         'updated_at' => $order->motor->updated_at ? $order->motor->updated_at->toISOString() : null,
            //     ] : null,
            //     'motor_part' => $order->motorPart ? [
            //         'id' => $order->motorPart->id,
            //         'service' => $order->motorPart->service,
            //         'price' => $order->motorPart->price,
            //         'subcategory_id' => $order->motorPart->subcategory_id,
            //         'subcategory' => $order->motorPart->subcategory ? [
            //             'id' => $order->motorPart->subcategory->id,
            //             'name' => $order->motorPart->subcategory->name,
            //             'category_id' => $order->motorPart->subcategory->category_id,
            //             'category' => $order->motorPart->subcategory->category ? [
            //                 'id' => $order->motorPart->subcategory->category->id,
            //                 'name' => $order->motorPart->subcategory->category->name,
            //             ] : null,
            //         ] : null,
            //     ] : null,
            //     'seals' => $order->seals->map(function ($seal) use ($order) {
            //         return [
            //             'order_id' => $order->id,
            //             'seal_id' => $seal->id,
            //             'seal' => [
            //                 'id' => $seal->id,
            //                 'cc_range' => $seal->cc_range,
            //                 'price' => $seal->price,
            //                 'qty' => $seal->qty,
            //                 'motor_id' => $seal->motor_id,
            //                 'gerai_id' => $seal->gerai_id,
            //                 'created_at' => $seal->created_at ? $seal->created_at->toISOString() : null,
            //                 'updated_at' => $seal->updated_at ? $seal->updated_at->toISOString() : null,
            //             ],
            //         ];
            //     })->toArray(),
            //     'gerai' => $order->gerai ? [
            //         'id' => $order->gerai->id,
            //         'name' => $order->gerai->name,
            //         'location' => $order->gerai->location,
            //         'created_at' => $order->gerai->created_at ? $order->gerai->created_at->toISOString() : null,
            //         'updated_at' => $order->gerai->updated_at ? $order->gerai->updated_at->toISOString() : null,
            //     ] : null,
            //     'pelanggan_id' => $order->customer ? $order->customer->id : null,
            //     'customer' => $order->customer ? [
            //         'id' => $order->customer->id,
            //         'nama' => $order->customer->nama,
            //         'no_wa' => $order->customer->no_wa,
            //         'gerai_id' => $order->customer->gerai_id,
            //         'plat' => $order->customer->plat,
            //         'layanan' => $order->customer->layanan,
            //         'subcategory' => $order->customer->subcategory,
            //         'motor' => $order->customer->motor,
            //         'bagian_motor' => $order->customer->bagian_motor,
            //         'harga_layanan' => $order->customer->harga_layanan,
            //         'harga_seal' => $order->customer->harga_seal,
            //         'total_harga' => $order->customer->total_harga,
            //         'seal' => $order->customer->seal,
            //         'info' => $order->customer->info,
            //         'sumber_info' => $order->customer->sumber_info,
            //         'status' => $order->customer->status->value,
            //         'warranty_claimed' => $order->customer->warranty_claimed,
            //         'created_at' => $order->customer->created_at ? $order->customer->created_at->toISOString() : null,
            //         'updated_at' => $order->customer->updated_at ? $order->customer->updated_at->toISOString() : null,
            //     ] : null,
            // ];

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
