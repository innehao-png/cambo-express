<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'total_amount' => $this->total_amount,
            'shipping_address' => $this->shipping_address,
            'tran_id' => $this->tran_id,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
            'order_items' => $this->whenLoaded('order_items', function () {
                return $this->order_items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'product' => $item->relationLoaded('product') ? [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'image' => $item->product->image
                                ? url(Storage::url($item->product->image))
                                : null,
                        ] : null,
                    ];
                });
            }),
        ];
    }
}
