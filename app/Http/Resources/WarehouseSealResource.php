<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseSealResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'ccRange' => $this->cc_range,
            'price' => $this->price,
            'qty' => $this->qty,
            'motor' => [
                'name' => $this->motor->name,
            ],
        ];
    }
}
