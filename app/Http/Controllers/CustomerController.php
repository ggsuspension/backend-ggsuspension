<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerSparepart;
use App\Models\Gerai;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Customer::with(['spareparts.sparepart', 'spareparts.gerai'])->get()->map(function ($customer) {
            return [
                'id' => $customer->id,
                'nama' => $customer->nama,
                'plat_motor' => $customer->plat_motor,
                'noWA' => $customer->noWA,
                'gerai' => $customer->gerai,
                'sudah_chat' => $customer->sudah_chat,
                'sumber_info' => $customer->sumber_info,
                'status' => $customer->status,
                'layanan' => $customer->layanan,
                'jenis_motor' => $customer->jenis_motor,
                'harga_service' => $customer->harga_service,
                'bagian_motor' => $customer->bagian_motor,
                'bagian_motor2' => $customer->bagian_motor2,
                'motor' => $customer->motor,
                'created_at' => $customer->created_at,
                'updated_at' => $customer->updated_at,
                'spareparts' => $customer->spareparts->map(function ($sp) {
                    return [
                        'id' => $sp->sparepart->id,
                        'sparepart_id' => $sp->sparepart_id,
                        'gerai_id' => $sp->gerai_id,
                        'gerai_nama' => $sp->gerai ? $sp->gerai->name : null,
                        'name' => $sp->sparepart->name,
                        'price' => $sp->price,
                        'qty' => $sp->qty,
                    ];
                })->toArray(),
            ];
        });
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'plat_motor' => 'required|string',
            'noWA' => 'required|string',
            'gerai' => 'required|string|exists:gerais,name',
            'sudah_chat' => 'sometimes',
            'sumber_info' => 'sometimes',
            'status' => 'sometimes|string',
            'layanan' => 'sometimes|string',
            'jenis_motor' => 'sometimes|string',
            'harga_service' => 'sometimes|integer',
            'bagian_motor' => 'sometimes|string',
            'bagian_motor2' => 'sometimes',
            'motor' => 'sometimes|string',
            'spareparts' => 'sometimes|array',
            'spareparts.*.sparepart_id' => 'required_with:spareparts|exists:spareparts,id',
            'spareparts.*.gerai_id' => 'required_with:spareparts|exists:gerais,id',
            'spareparts.*.qty' => 'required_with:spareparts|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            if ($request->has('spareparts')) {
                if (empty($request->gerai)) {
                    throw new \Exception("Nama gerai tidak boleh kosong.");
                }
                Log::info('Mencari gerai dengan nama: ' . $request->gerai);
                $gerai = Gerai::where('name', $request->gerai)->first();
                if (!$gerai) {
                    throw new \Exception("Gerai {$request->gerai} tidak ditemukan di tabel gerais.");
                }
                foreach ($request->spareparts as $sparepart) {
                    if ($sparepart['gerai_id'] != $gerai->id) {
                        throw new \Exception("gerai_id pada spareparts tidak sesuai dengan gerai pelanggan.");
                    }
                }
            }

            $customer = Customer::create($request->except('spareparts'));
            if ($request->has('spareparts')) {
                foreach ($request->spareparts as $sparepart) {
                    $sparepartData = Sparepart::findOrFail($sparepart['sparepart_id']);
                    if ($sparepartData->qty < $sparepart['qty']) {
                        throw new \Exception("Stok sparepart {$sparepartData->type} - {$sparepartData->size} tidak cukup.");
                    }
                    CustomerSparepart::create([
                        'customer_id' => $customer->id,
                        'sparepart_id' => $sparepart['sparepart_id'],
                        'gerai_id' => $sparepart['gerai_id'],
                        'qty' => $sparepart['qty'],
                        'price' => $sparepartData->price,
                    ]);
                    $sparepartData->decrement('qty', $sparepart['qty']);
                }
            }

            $harga_sparepart = CustomerSparepart::where('customer_id', $customer->id)
                ->sum(DB::raw('qty * price'));
            $customer->update(['harga_sparepart' => $harga_sparepart]);
            DB::commit();
            return response()->json(['success' => true, 'data' => $customer->load('spareparts.sparepart', 'spareparts.gerai')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create customer: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        Log::info('Update customer request:', ['request' => $request->all(), 'customer_id' => $customer->id, 'customer_gerai' => $customer->gerai]);

        $validated = $request->validate([
            'nama' => 'sometimes|string',
            'plat_motor' => 'sometimes|string',
            'noWA' => 'sometimes|string',
            'gerai' => 'sometimes|string|exists:gerais,name',
            'sudah_chat' => 'sometimes|boolean',
            'sumber_info' => 'sometimes|string',
            'status' => 'sometimes',
            'layanan' => 'sometimes|string',
            'jenis_motor' => 'sometimes|string',
            'harga_service' => 'sometimes|numeric',
            'harga_sparepart' => 'sometimes|numeric',
            'bagian_motor' => 'sometimes|string',
            'bagian_motor2' => 'sometimes',
            'motor' => 'sometimes|string',
            'spareparts' => 'sometimes|array',
            'spareparts.*.sparepart_id' => 'required_with:spareparts|exists:spareparts,id',
            'spareparts.*.gerai_id' => 'required_with:spareparts|exists:gerais,id',
            'spareparts.*.qty' => 'required_with:spareparts|integer|min:1',
        ]);

        try {
            DB::beginTransaction();
            // Update data customer
            $customer->update($request->only([
                'nama',
                'plat_motor',
                'noWA',
                'gerai',
                'sudah_chat',
                'sumber_info',
                'status',
                'layanan',
                'jenis_motor',
                'harga_service',
                'harga_sparepart',
                'bagian_motor',
                'bagian_motor2',
                'motor'
            ]));

            // Handle spareparts jika ada
            if ($request->has('spareparts')) {
                // Validasi gerai_id sesuai dengan gerai pelanggan
                $geraiName = $request->has('gerai') ? $request->gerai : $customer->gerai;
                if (empty($geraiName)) {
                    throw new \Exception("Nama gerai tidak boleh kosong.");
                }
                Log::info('Mencari gerai dengan nama: ' . $geraiName);
                $gerai = $customer->gerai()->first();
                if (!$gerai) {
                    throw new \Exception("Gerai {$geraiName} tidak ditemukan di tabel gerais.");
                }
                foreach ($request->spareparts as $index => $sparepart) {
                    if (!isset($sparepart['gerai_id'])) {
                        throw new \Exception("gerai_id pada spareparts[{$index}] tidak disertakan.");
                    }
                    if ($sparepart['gerai_id'] != $gerai->id) {
                        throw new \Exception("gerai_id pada spareparts[{$index}] ({$sparepart['gerai_id']}) tidak sesuai dengan gerai pelanggan ({$geraiName}, ID: {$gerai->id}).");
                    }
                }

                // Hapus sparepart yang tidak ada di request baru
                CustomerSparepart::where('customer_id', $customer->id)
                    ->whereNotIn('sparepart_id', collect($validated['spareparts'])->pluck('sparepart_id'))
                    ->delete();

                // Tambah atau update sparepart
                foreach ($validated['spareparts'] as $sparepart) {
                    $sparepartData = Sparepart::findOrFail($sparepart['sparepart_id']);
                    CustomerSparepart::updateOrCreate(
                        [
                            'customer_id' => $customer->id,
                            'sparepart_id' => $sparepart['sparepart_id'],
                            'gerai_id' => $sparepart['gerai_id'],
                        ],
                        [
                            'qty' => $sparepart['qty'],
                            'price' => $sparepartData->price,
                        ]
                    );
                }

                // Hitung ulang harga_sparepart
                $harga_sparepart = CustomerSparepart::where('customer_id', $customer->id)
                    ->sum(DB::raw('qty * price'));
                $customer->update(['harga_sparepart' => $harga_sparepart]);
            }

            DB::commit();

            // Muat ulang customer dengan relasi spareparts dan gerai
            $customer->load('spareparts.sparepart', 'spareparts.gerai');
            return response()->json(['success' => true, 'data' => $customer], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update customer: ' . $e->getMessage(), ['request' => $request->all(), 'customer_id' => $customer->id]);
            return response()->json(['success' => false, 'message' => 'Failed to update customer: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
