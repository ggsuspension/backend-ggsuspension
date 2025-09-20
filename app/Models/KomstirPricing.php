<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KomstirPricing extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'komstir_pricings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'motor_id',
        'name',
        'part_type',
        'price',
    ];

    /**
     * Mendefinisikan relasi bahwa setiap harga komstir
     * dimiliki oleh satu motor spesifik.
     */
    public function motor(): BelongsTo
    {
        return $this->belongsTo(Motor::class, 'motor_id');
    }
}
