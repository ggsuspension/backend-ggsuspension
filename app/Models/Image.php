<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
       use HasFactory;

    /**
     * Atribut yang dapat diisi (mass-assignable)
     */
    protected $guarded = ["id"];

    /**
     * Dapatkan URL gambar yang bisa diakses publik
     */
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }
}
