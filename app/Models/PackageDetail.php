<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PackageDetail extends Model
{
    protected $table = 'package_details';
    protected $guarded = ['id'];

    /**
     * Relasi ke ServicePackage untuk mengetahui detail ini milik paket mana.
     */
    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }

    /**
     * Relasi ke Category untuk mengetahui jenis layanan (e.g., Rebound).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke MotorPart untuk mengetahui part mana yang diservis.
     */
    public function motorPart(): BelongsTo
    {
        return $this->belongsTo(MotorPart::class);
    }

    /**
     * Relasi ke stok spare part di gerai (tabel seals) yang termasuk dalam layanan ini.
     * Saya beri nama 'includedOutletParts' agar lebih jelas fungsinya.
     */
    public function includedOutletParts(): BelongsToMany
    {
        return $this->belongsToMany(Seal::class, 'package_detail_seal')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
