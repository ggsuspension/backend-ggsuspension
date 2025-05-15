<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded=['id'];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

      public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }
}
