<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcategory extends Model
{
    protected $table = 'subcategories';
    protected $guarded = ['id'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_motor_type');
    }

    public function motors(): HasMany
    {
        return $this->hasMany(Motor::class);
    }

    public function motorParts(): HasMany
    {
        return $this->hasMany(MotorPart::class);
    }
}
