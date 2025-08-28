<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorySparepart extends Model
{
    protected $guarded=['id'];
    protected $table = 'history_spareparts';
    public function gerai(){
        return $this->belongsToMany(Gerai::class);
    }
    public function stockRequest(){
        return $this->belongsTo(StockRequest::class);
    }
    
}
