<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $fillable = [
        'nama',
        'plat_motor',
        'noWA',
        'gerai',
        'sudah_chat',
        'sumber_info',
        'status',
        'sparepart',
        'layanan',
        'jenis_motor',
        'harga_service',
        'harga_sparepart',
        'bagian_motor',
        'bagian_motor2',
        'motor',
        'created_at',
        'updated_at'
    ];
    protected $guarded = ['id'];
    protected $casts = [
        'sparepart' => 'array'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if ($customer->gerai && !Gerai::where('name', $customer->gerai)->exists()) {
                throw new \Exception("Gerai {$customer->gerai} tidak ditemukan.");
            }
        });

        static::updating(function ($customer) {
            if ($customer->gerai && !Gerai::where('name', $customer->gerai)->exists()) {
                throw new \Exception("Gerai {$customer->gerai} tidak ditemukan.");
            }
        });
    }

    public function gerai()
    {
        return $this->hasOne(Gerai::class, 'name', 'gerai');
        // return $this->belongsTo(Gerai::class, 'gerai_id');
    }
}
