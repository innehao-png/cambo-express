<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    use HasFactory;

    const LOW_STOCK_THRESHOLD = 10;

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

    public function stocks()
    {
        return $this->hasMany(StockLog::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeWhenCategory(Builder $query, mixed $categoryId): Builder
    {
        return ($categoryId && $categoryId !== 'null')
            ? $query->where('category_id', $categoryId)
            : $query;
    }

    public function scopeWhenSearch(Builder $query, ?string $search): Builder
    {
        return ($search && $search !== 'null')
            ? $query->where(fn(Builder $q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%"))
            : $query;
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->where('stock', '<=', self::LOW_STOCK_THRESHOLD);
    }
}
