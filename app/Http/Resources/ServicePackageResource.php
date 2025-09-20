<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServicePackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_paket' => $this->name,
            'type_service' => $this->type_service,
            'jenis_motor' => $this->whenLoaded('motorType', function () {
                return $this->motorType->name;
            }),
            'layanan' => $this->whenLoaded('details', function () {
                // Eager load relasi untuk efisiensi
                $this->details->load('includedOutletParts');

                return $this->details->map(function ($detail) {
                    return [
                        'layanan' => $detail->category->name,
                        'part_motor' => $detail->motorPart?->name ?? 'Jasa Servis',
                        'cc_range' => $detail->motorPart?->cc_range,
                        'warranty' => $detail->warranty,
                        'price' => $detail->price
                    ];
                });
            }),
            'motor' => $this->when(
                // KONDISI: Hanya tampilkan jika type_service adalah 'KOMSTIR'
                strtoupper($this->type_service) === 'KOMSTIR',
                // JIKA BENAR: Tampilkan daftar motor spesifik
                $this->whenLoaded('motors', function () {
                    return $this->motors->map(function ($motor) {
                        return [
                            'name' => $motor->name,
                        ];
                    });
                }),
                // JIKA SALAH (misal: 'SUSPENSI'): Kembalikan array kosong
                []
            ),
            'total_price' => $this->total_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
