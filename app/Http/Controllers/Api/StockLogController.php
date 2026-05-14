<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockLog;
use App\Traits\IsAdmin;
use Illuminate\Http\Request;

class StockLogController extends Controller
{
    use IsAdmin;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        StockLog::create([
            'product_id'=> $request->product_id,
            'quantity' => $request->quantity,
            'type' => $request->type,
            'note' => $request->note ?? null
        ]);

        if($request->type === 'in'){
            $product->increment('stock', $request->quantity);
        }else{
            if($request->quantity > $product->stock){
                return $this->errorResponse('Product out of stock',500);
            }
            $product->decrement('stock', $request->quantity);
        }

        return $this->successResponse(['message' => 'Stock updated']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
