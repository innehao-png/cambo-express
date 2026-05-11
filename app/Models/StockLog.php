<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    protected $table = 'stocklogs';
    protected $fillable = [
        'product_id',
        'quantity',
        'type',
        'note'
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
