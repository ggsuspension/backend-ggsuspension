<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotorPart extends Model
{
    protected $guarded=['id'];
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function motors()
    {
        return $this->belongsToMany(Motor::class, 'motor_to_motor_part')
            ->withPivot([]);
    }
      public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }
}
