<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seal extends Model
{
    use HasFactory;

    protected $table = 'seals';
    protected $guarded = ['id'];

    public function motor()
    {
        return $this->belongsTo(Motor::class, 'motor_id');
    }

    public function gerai()
    {
        return $this->belongsTo(Gerai::class, 'gerai_id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_to_seals');
    }
}
