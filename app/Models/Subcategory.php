<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $fillable = ['name', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function motorParts()
    {
        return $this->hasMany(MotorPart::class);
    }
      public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }
}
