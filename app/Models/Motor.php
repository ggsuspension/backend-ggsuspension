<?php

namespace App\Models;

use App\Http\Resources\WarehouseSealResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Motor extends Model
{
    protected $table = 'motors';
    protected $guarded = ['id'];

    /**
     * Mendefinisikan relasi "belongs to" ke model MotorType.
     * Setiap Motor dimiliki oleh satu MotorType.
     */
    public function motorType(): BelongsTo
    {
        return $this->belongsTo(MotorType::class, 'motor_type_id');
    }

    /**
     * Relasi many-to-many ke service package yang berlaku untuk motor ini.
     */
    public function servicePackages(): BelongsToMany
    {
        return $this->belongsToMany(ServicePackage::class, 'service_package_motors');
    }

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
