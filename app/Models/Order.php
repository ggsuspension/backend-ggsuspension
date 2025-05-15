<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders'; // Pastikan nama tabel benar
    protected $primaryKey = 'id'; // Pastikan primary key adalah 'id'
    protected $keyType = 'int'; // Tipe primary key
    public $incrementing = true;

    protected $casts = [
        'status' => OrderStatus::class,
        'waktu' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected $fillable = [
        'nama',
        'plat',
        'no_wa',
        'waktu',
        'gerai_id',
        'total_harga',
        'status',
        'motor_id',
        'motor_part_id',
        'cancelled_at',
    ];

    public function gerai()
    {
        return $this->belongsTo(Gerai::class);
    }

    public function motor()
    {
        return $this->belongsTo(Motor::class);
    }

    public function motorPart()
    {
        return $this->belongsTo(MotorPart::class);
    }

    public function seals()
    {
        return $this->belongsToMany(Seal::class, 'order_to_seals')
            ->withPivot(['order_id', 'seal_id'])
            ->with(['motor']);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'plat', 'plat');
    }
}
