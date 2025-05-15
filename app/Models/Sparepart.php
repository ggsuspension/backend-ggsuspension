<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    protected $guarded=['id'];


    public function motor()
    {
        return $this->belongsTo(Motor::class, 'motor_id');
    }

    public function requests()
    {
        return $this->hasMany(StockRequest::class);
    }
}
