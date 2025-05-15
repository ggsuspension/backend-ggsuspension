<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Model;

class StockRequest extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'status' => RequestStatus::class,
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function seal()
    {
        return $this->belongsTo(Seal::class);
    }

    public function gerai()
    {
        return $this->belongsTo(Gerai::class);
    }

    public function warehouseSeal()
    {
        return $this->belongsTo(Sparepart::class, 'sparepart_id');
    }
}
