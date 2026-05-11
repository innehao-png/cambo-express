<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        return Order::with('order_items.product')
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function show($id)
    {
        return Order::with('order_items.product')
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->findOrFail($id);
    }

    public function getOrdersByUserID($id)
    {
        $user = auth()->user();

        if ($user->id != $id && (!$user->role || $user->role->name !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return Order::with('order_items.product')
            ->where('user_id', $id)
            ->orderByDesc('created_at')
            ->paginate(15);
    }
}
