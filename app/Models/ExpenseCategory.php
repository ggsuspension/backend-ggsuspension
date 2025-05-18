<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $table = 'expense_categories';
    protected $fillable = ['name', 'monthly_cost', 'daily_cost'];

    protected $casts = [
        'daily_cost' => 'decimal:2',
        'monthly_cost' => 'decimal:2',
    ];
}
