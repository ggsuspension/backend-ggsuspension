<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    protected $guarded=['id'];
    protected $casts = [
        'sparepart'=>'array'
    ];
    public function customer(){
        return $this->hasOne(Customer::class);
    }
}
