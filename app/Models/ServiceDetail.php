<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServiceDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     * Menggunakan $guarded = [] adalah cara cepat untuk mengizinkan semua kolom diisi.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     * Ini akan secara otomatis mengubah kolom JSON 'sparepart_include' menjadi array PHP.
     *
     * @var array
     */
    protected $casts = [
        'sparepart_include' => 'array',
    ];

    /**
     * Mendefinisikan relasi "many-to-many" ke model ServicePackage.
     * Sebuah detail layanan bisa dimiliki oleh banyak paket layanan.
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(ServicePackage::class, 'package_details');
    }
}
