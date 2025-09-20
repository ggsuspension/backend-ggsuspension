<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MotorPart extends Model
{
    protected $guarded = ['id'];

    /**
     * Mendefinisikan relasi "belongs to" ke model MotorType.
     * Setiap MotorPart dimiliki oleh satu MotorType.
     */
    public function motorType(): BelongsTo
    {
        return $this->belongsTo(MotorType::class);
    }

    /**
     * Mendefinisikan relasi many-to-many ke Category.
     * Part ini bisa ada di banyak Category (layanan) dengan harga berbeda.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_motor_part')
            ->withPivot('price')
            ->withTimestamps();
    }

    // public function subcategory()
    // {
    //     return $this->belongsTo(Subcategory::class);
    // }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function motors()
    {
        return $this->belongsToMany(Motor::class, 'motor_to_motor_part')
            ->withPivot([]);
    }
}
