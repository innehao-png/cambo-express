<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Get user's cart
    public function index(Request $request)
    {
        $cart = Cart::with(['user', 'cart_items.product'])
            ->where('user_id', Auth::id())->first();

        if (!$cart || $cart->cart_items->isEmpty()) {
            return collect([
                'data' => [
                    'cart' => [],
                    'subtotal' => 0,
                    'message' => 'Cart is empty'
                ]
            ])->toJson();
        }

        $subtotal = 0;
        $total = 0;

        foreach ($cart->cart_items as $item) {
            $subtotal += $item->quantity * $item->product->price;
            $total += $item->quantity;
        }

        return $this->successResponse([
            'data' => new CartResource($cart),
            'subtotal' => $subtotal,
            'total' => $total
        ]);
    }

    // Add item to cart
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1']
        ]);

        $user = $request->user();

        if (!$user) {
            return $this->unauthorizedResponse();
        }

        $product = Product::findOrFail($validated['product_id']);

        // Check stock
        if ($product->stock < $validated['quantity']) {
            return $this->errorResponse('Not enough stock available', 400);
        }

        // Create or get cart
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id
        ]);

        // Find cart item
        $cartItem = $cart->cart_items()
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {

            $newQty = $cartItem->quantity + $validated['quantity'];

            if ($product->stock < $newQty) {
                return $this->errorResponse('Not enough stock available', 400);
            }

            $cartItem->update([
                'quantity' => $newQty
            ]);
        } else {

            $cartItem = $cart->cart_items()->create([
                'product_id' => $product->id,
                'quantity' => $validated['quantity']
            ]);
        }

        $cartItem->load('product');

        return $this->successResponse([
            'item' => $cartItem
        ], 'Product added to cart successfully');
    }

    // Update cart item quantity
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::with('cart')->find($id);

        if (!$cartItem) {
            return $this->notFoundResponse('Cart item not found');
        }

        if ($cartItem->cart->user_id !== Auth::id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $product = $cartItem->product;

        if ($product->stock < $request->quantity) {
            return $this->errorResponse('Not enough stock available', 400);
        }

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return $this->successResponse([
            'item' => $cartItem->load('product')
        ], 'Cart item updated successfully');
    }

    // Remove item from cart
    public function destroy($id)
    {
        $cartItem = CartItem::with('cart')->find($id);

        if (!$cartItem) {
            return $this->notFoundResponse('Cart item not found');
        }

        if ($cartItem->cart->user_id !== Auth::id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $cartItem->delete();

        return $this->successResponse([], 'Cart item removed successfully');
    }

    // Clear entire cart
    public function clear(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorizedResponse();
        }

        // Get the user's cart
        $cart = Cart::where('user_id', $user->id)->first();

        // dd($cart);

        if (!$cart) {
            return $this->notFoundResponse('No cart found for this user');
        }

        // Delete all items in the cart
        CartItem::where('cart_id', $cart->id)->delete();

        return $this->successResponse([], 'Cart has been successfully cleared.');
    }
}
