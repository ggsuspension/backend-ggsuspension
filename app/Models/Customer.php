<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $guarded = ['id'];
    protected $casts = [
        'sparepart' => 'array'
    ];

    public function gerai()
    {
        return $this->belongsTo(Gerai::class, 'gerai_id');
    }

}
