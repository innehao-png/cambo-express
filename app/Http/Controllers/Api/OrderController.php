<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $order = Order::with('order_items.product')
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(3);

        return OrderResource::collection($order)->additional([
            'message' => 'Order retrieved successfully'
        ]);
    }

    public function show($id)
    {
        $order = Order::with('order_items.product')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return new OrderResource($order);
    }

    public function getOrdersByUserID($id)
    {
        $user = auth()->user();

        if ($user->id != $id && (!$user->role || $user->role->name !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $orders = Order::with('order_items.product')
            ->where('user_id', $id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return OrderResource::collection($orders);
    }
}
