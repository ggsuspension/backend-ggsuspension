<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSparepart extends Model
{
    protected $fillable = ['customer_id', 'sparepart_id', 'gerai_id', 'qty', 'price'];

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class, 'sparepart_id');
    }

    public function gerai()
    {
        return $this->belongsTo(Gerai::class, 'gerai_id');
    }
}
