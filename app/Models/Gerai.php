<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gerai extends Model
{
    protected $table = 'gerais';
    protected $guarded=['id'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function seals()
    {
        return $this->hasMany(Seal::class, 'gerai_id');
    }
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function pelanggans()
    {
        return $this->hasMany(Customer::class);
    }

    public function stockRequests()
    {
        return $this->hasMany(StockRequest::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function expenseCategories()
    {
        return $this->belongsToMany(ExpenseCategory::class, 'expense_category_gerai')
            ->withPivot('daily_cost', 'monthly_cost')
            ->withTimestamps();
    }

    public function netRevenues()
    {
        return $this->hasMany(DailyNetRevenue::class);
    }
}
