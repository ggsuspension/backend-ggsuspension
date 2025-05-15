<?php

namespace App\Models;

use App\Http\Resources\WarehouseSealResource;
use Illuminate\Database\Eloquent\Model;

class Motor extends Model
{
    protected $table = 'motors';
    protected $guarded = ['id'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function seals()
    {
        return $this->hasMany(Seal::class);
    }

    public function motorParts()
    {
        return $this->belongsToMany(MotorPart::class, 'motor_to_motor_part');
    }

    public function warehouseSeals()
    {
        return $this->hasMany(WarehouseSealResource::class);
    }
}
