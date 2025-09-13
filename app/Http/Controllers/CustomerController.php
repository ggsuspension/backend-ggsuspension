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
        $customers = Customer::with(['customerSpareparts.sparepart', 'customerSpareparts.gerai'])->get();

        $data = $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'nama' => $customer->nama,
                'plat_motor' => $customer->plat_motor,
                'noWA' => $customer->noWA,
                'gerai' => $customer->gerai,
                'status' => $customer->status,
                'layanan' => $customer->layanan,
                'jenis_motor' => $customer->jenis_motor,
                'harga_service' => $customer->harga_service,
                'harga_sparepart' => $customer->harga_sparepart,
                'bagian_motor' => $customer->bagian_motor,
                'bagian_motor2' => $customer->bagian_motor2,
                'motor' => $customer->motor,
                'sumber_info' => $customer->sumber_info,
                'sudah_chat' => $customer->sudah_chat,
                'created_at' => $customer->created_at,
                'data_lainnya' => $customer->data_lainnya,
                'klaim_garansi' => $customer->klaim_garansi,
                'spareparts' => $customer->customerSpareparts->map(function ($cs) {
                    $name = $cs->name ?: ($cs->sparepart ? $cs->sparepart->name : 'Sparepart tidak ditemukan');

                    return [
                        'customer_sparepart_id' => $cs->id,
                        'sparepart_id' => $cs->sparepart_id,
                        'gerai_id' => $cs->gerai_id,
                        'qty' => $cs->qty,
                        'price' => $cs->price,
                        'name' => $name,
                    ];
                }),
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
        $request->validate([
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
            'keterangan' => 'sometimes|nullable|string',
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

            $customerData = $request->except('spareparts');
            $customerData['keterangan'] = $request->input('keterangan', null);

            $customer = Customer::create($customerData);

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
            return response()->json(['success' => true, 'data' => $customer->load('customerSpareparts.sparepart', 'customerSpareparts.gerai')], 201);
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
        Log::info('Update customer request:', ['request' => $request->all(), 'customer_id' => $customer->id]);

        $validated = $request->validate([
            'nama' => 'sometimes|string',
            'plat_motor' => 'sometimes|string',
            'noWA' => 'sometimes|string',
            'status' => 'sometimes|string',
            'layanan' => 'sometimes|nullable|string',
            'jenis_motor' => 'sometimes|nullable|string',
            'harga_service' => 'sometimes|numeric',
            'bagian_motor' => 'sometimes|nullable|string',
            'bagian_motor2' => 'sometimes|nullable|string',
            'motor' => 'sometimes|nullable|string',
            'spareparts' => 'sometimes|array',
            'spareparts.*.sparepart_id' => 'nullable|integer|exists:spareparts,id',
            'spareparts.*.name' => 'required|string|max:255',
            'spareparts.*.gerai_id' => 'required_with:spareparts|exists:gerais,id',
            'spareparts.*.qty' => 'required_with:spareparts|integer|min:1',
            'spareparts.*.price' => 'required_with:spareparts|numeric',
            'keterangan' => 'sometimes|string',
        ]);

        try {
            DB::beginTransaction();

            $customer->update($request->except('spareparts'));

            if ($request->has('spareparts')) {
                $customer->customerSpareparts()->delete();

                foreach ($validated['spareparts'] as $sparepart) {

                    CustomerSparepart::create([
                        'customer_id'  => $customer->id,
                        'gerai_id'     => $sparepart['gerai_id'],
                        'qty'          => $sparepart['qty'],
                        'price'        => $sparepart['price'],
                        'sparepart_id' => $sparepart['sparepart_id'],
                        'name'         => $sparepart['name'],
                    ]);
                }
            }

            $totalHargaSparepart = $customer->customerSpareparts()->sum(DB::raw('qty * price'));
            $customer->update([
                'harga_sparepart' => $totalHargaSparepart,
            ]);

            DB::commit();

            $customer->load('customerSpareparts.sparepart', 'customerSpareparts.gerai');
            return response()->json(['success' => true, 'message' => 'Data antrian berhasil diperbarui.', 'data' => $customer], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui antrian: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'customer_id' => $customer->id
            ]);
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui antrian: ' . $e->getMessage()], 500);
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
