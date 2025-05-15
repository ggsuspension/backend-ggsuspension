<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyNetRevenue extends Model
{
    protected $fillable = [
        'gerai_id',
        'date',
        'total_revenue',
        'total_expenses',
        'net_revenue',
    ];

    protected $casts = [
        'date' => 'date',
        'total_revenue' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_revenue' => 'decimal:2',
    ];


    public function gerai()
    {
        return $this->belongsTo(Gerai::class);
    }
}
