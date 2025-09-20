<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $guarded = ['id'];

    /**
     * Mendefinisikan relasi many-to-many ke model MotorType.
     * Satu Category (layanan) bisa tersedia untuk banyak MotorType.
     */
    public function motorTypes(): BelongsToMany
    {
        return $this->belongsToMany(MotorType::class, 'category_motor_type');
    }

    /**
     * Mendefinisikan relasi many-to-many ke MotorPart.
     * Category ini memiliki banyak MotorPart dengan harga tertentu.
     */
    public function motorParts(): BelongsToMany
    {
        return $this->belongsToMany(MotorPart::class, 'category_motor_part')
            ->withPivot('price')
            ->withTimestamps();
    }
}
