<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServicePackage extends Model
{
    use HasFactory;

    /**
     * Mengizinkan mass assignment untuk semua kolom.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Relasi ke MotorType untuk mengetahui paket ini untuk jenis motor apa (misal: Matic, Bebek).
     * Relasi ini tetap relevan.
     */
    public function motorType(): BelongsTo
    {
        return $this->belongsTo(MotorType::class);
    }

    /**
     * [PERUBAHAN UTAMA] Relasi many-to-many ke ServiceDetail.
     * Sebuah paket terdiri dari BANYAK detail layanan, dan sebuah detail bisa ada di BANYAK paket.
     * Laravel akan mencari pivot table 'package_details' secara otomatis jika mengikuti konvensi.
     */
    public function details()
    {
        return $this->hasMany(PackageDetail::class, 'service_package_id');
    }

    /**
     * Relasi many-to-many ke motor spesifik yang berlaku untuk paket ini.
     * Relasi ini juga tetap relevan.
     */
    public function motors(): BelongsToMany
    {
        return $this->belongsToMany(Motor::class, 'service_package_motors');
    }
}
