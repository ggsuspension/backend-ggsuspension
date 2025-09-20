<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MotorType extends Model
{
    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'motor_types';

    protected $guarded = ['id'];

    /**
     * Mendefinisikan relasi one-to-many ke model Motor.
     * Satu MotorType bisa memiliki banyak Motor.
     */
    public function motors(): HasMany
    {
        return $this->hasMany(Motor::class);
    }

    /**
     * Mendefinisikan relasi one-to-many ke model MotorPart.
     * Satu MotorType bisa memiliki banyak MotorPart.
     */
    public function motorParts(): HasMany
    {
        return $this->hasMany(MotorPart::class);
    }

    /**
     * Mendefinisikan relasi many-to-many ke model Category.
     * Satu MotorType bisa terkait dengan banyak Category (layanan).
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_motor_type');
    }
}
