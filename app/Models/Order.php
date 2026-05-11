<?php

namespace App\Models;

use App\Models\StockLog;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_amount',
        'shipping_address',
        'tran_id',
        'status',
        'payment_method',
        'paid_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    protected $cast = [
        'paid_at' => 'datetime'
    ];

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        foreach($this->order_items as $item){
            if($item->product){
                $item->product->decrement('stock', $item->quantity);

                StockLog::create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'type' => 'out',
                    'note' => "Order #" . $this->id . 'Paid'
                ]);
            }
        }
    }
}
