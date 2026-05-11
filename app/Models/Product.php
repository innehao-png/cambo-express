<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'category_id',
        'description',
        'price',
        'stock',
        'discount',
        'status',
        'image'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function cart_item()
    {
        return $this->belongsTo(CartItem::class);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stocks(){
        return $this->hasMany(StockLog::class);
    }
}
