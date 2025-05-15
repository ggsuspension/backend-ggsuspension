<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderToSeal extends Pivot
{
    public $timestamps = false;

    protected $table = 'order_to_seals';
}
