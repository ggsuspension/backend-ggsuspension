<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCustomer extends Model
{
    protected $guarded = ["id"];
    public function service_customer(){
        return $this->hasOne(Customer::class);
    }
}
