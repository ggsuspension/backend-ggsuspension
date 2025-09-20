<?php

namespace App\Http\Controllers;

use App\Models\Seal;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SealController extends Controller
{
    public function getAllSeals(): JsonResponse
    {
        $seals = Seal::with(['motor', 'gerai'])->get();
        return response()->json($seals);
    }

    public function getSeal(Seal $seal): JsonResponse
    {
        return response()->json($seal->load(['motor', 'gerai']));
    }

    public function getSealsByGerai(int $geraiId): JsonResponse
    {
        $seals = Seal::where('gerai_id', $geraiId)->with(['motor'])->get();
        return response()->json($seals);
    }

    public function getSealsByGeraiId(int $geraiId): JsonResponse
    {
        $seals = Seal::where('gerai_id', $geraiId)->get();
        return response()->json($seals);
    }

    /**
     * Fungsi BARU untuk membuat entri seal baru di sebuah gerai.
     * Akan dipanggil ketika frontend melakukan INSERT.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'gerai_id' => 'required|integer|exists:gerais,id',
                'sparepart_id' => 'required|integer|exists:spareparts,id',
                'qty' => 'required|integer|min:0',
            ]);

            $existingSeal = Seal::where('gerai_id', $validated['gerai_id'])
                ->where('sparepart_id', $validated['sparepart_id'])
                ->first();

            if ($existingSeal) {
                $existingSeal->increment('qty', $validated['qty']);
                return response()->json(['data' => $existingSeal], 200);
            }

            $masterSparepart = Sparepart::find($validated['sparepart_id']);

            if (!$masterSparepart) {
                return response()->json(['error' => 'Sparepart master tidak ditemukan.'], 404);
            }

            $dataToCreate = [
                'gerai_id' => $validated['gerai_id'],
                'sparepart_id' => $validated['sparepart_id'],
                'qty' => $validated['qty'],
                'category' => $masterSparepart->category,
                'name' => $masterSparepart->name,
                'price' => $masterSparepart->price,
                'purchase_price' => $masterSparepart->purchase_price,
                'motor_id' => $masterSparepart->motor_id,
            ];

            $seal = Seal::create($dataToCreate);

            return response()->json(['data' => $seal], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }


    /**
     * Fungsi yang DIPERBAIKI untuk memperbarui kuantitas seal yang sudah ada.
     */
    public function updateSeal(Request $request, Seal $seal): JsonResponse
    {
        $validated = $request->validate([
            'qty' => 'required|integer|min:0',
        ]);

        // Langsung update model $seal yang didapat dari route model binding.
        $seal->update($validated);

        // Kembalikan data seal yang sudah diperbarui.
        return response()->json(['data' => $seal->fresh()]);
    }


    public function deleteSeal(Seal $seal): JsonResponse
    {
        $seal->delete();
        return response()->json(null, 204);
    }

    /**
     * Mengambil daftar sparepart (seals) yang tersedia di gerai user
     * berdasarkan kategori dan ID motor.
     */
    public function getByCategoryAndMotor(Request $request)
    {
        // 1. Validasi input dari request frontend
        $validated = $request->validate([
            'category' => 'required|string',
            'motor_id' => 'required|integer|exists:motors,id',
        ]);

        // 2. Dapatkan gerai_id dari user yang sedang login
        // PENTING: Pastikan model User Anda memiliki informasi gerai_id.
        $geraiId = Auth::user()->gerai_id;

        if (!$geraiId) {
            // Beri respons error jika user tidak terasosiasi dengan gerai manapun
            return response()->json(['message' => 'User tidak terhubung dengan gerai.'], 403);
        }

        // 3. Lakukan query ke tabel 'seals'
        $availableSeals = Seal::where('category', $validated['category'])
            ->where('motor_id', $validated['motor_id'])
            ->where('gerai_id', $geraiId)
            ->where('qty', '>', 0) // PENTING: Hanya ambil part yang stoknya ada
            ->get();

        // 4. Kirim hasilnya sebagai respons JSON
        return response()->json($availableSeals);
    }
}
