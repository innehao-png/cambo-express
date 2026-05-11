<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\IsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use IsAdmin;
    // Get all products with optional filters
    public function index(Request $request)
    {
        $categoryId = $request->get('category_id') ?? $request->get('category');
        $search = $request->get('search');
        $sort = $request->filled('sort') && $request->sort !== 'null' ? $request->sort : 'id';
        $order = $request->filled('order') && $request->order !== 'null' ? $request->order : 'desc';
        $perPage = $request->get('per_page', 15);

        $products = Product::with('category')
            ->where('status', 'active')
            ->when($categoryId && $categoryId !== 'null', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($search && $search !== 'null', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $order)
            ->paginate($perPage);

        return ProductResource::collection($products)->additional([
            'message' => 'Products retrieved successfully'
        ]);
    }

    // Get single product
    public function show($id)
    {
        $product = Product::with('category', 'order_items')->find($id);

        if (!$product) {
            return $this->notFoundResponse('Product not found');
        }

        return $this->successResponse(
            ProductResource::collection($product),
            'Product retrieved successfully'
        );
    }

    // Create product (admin only)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'discount'    => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            ...$validated,
            'status' => 'active',
        ]);

        return $this->successResponse(
            new ProductResource($product),
            'Product created successfully',
            201
        );
    }

    // Update product (admin only)
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->notFoundResponse('Product not found');
        }

        $validated = $request->validate([
            'name'        => 'string|max:255',
            'category_id' => 'exists:categories,id',
            'description' => 'string',
            'price'       => 'numeric|min:0',
            'stock'       => 'integer|min:0',
            'discount'    => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'status'      => 'in:active,inactive',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return $this->successResponse(
            $product,
            'Product updated successfully'
        );
    }

    // Delete product (admin only)
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->notFoundResponse('Product not found');
        }

        $product->delete();

        return $this->successResponse([], 'Product deleted successfully');
    }
}
