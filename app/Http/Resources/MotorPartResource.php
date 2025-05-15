<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MotorPartResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'service' => $this->service,
            'price' => $this->price,
            'subcategory' => [
                'id' => $this->subcategory->id,
                'name' => $this->subcategory->name,
                'category' => $this->subcategory->category->name,
            ],
            'motors' => $this->motors->pluck('name'), // pastikan kolom nama motor adalah 'name'
            'orders' => $this->orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'nama' => $order->nama, // pastikan kolom nama di order adalah 'nama'
                ];
            }),
        ];
    }
}
