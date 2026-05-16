<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\IsAdmin;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    use IsAdmin;
    // Get all products with optional filters
    public function index(Request $request): AnonymousResourceCollection
    {
        [$sort, $order, $perPage] = $this->resolveSortingParams($request);

        $cacheKey = 'products_index_' . md5(json_encode($request->all()));

        $products = Cache::remember($cacheKey, 60, function () use ($request, $sort, $order, $perPage) {
            return Product::with('category')
                ->active()
                ->whenCategory($request->input('category_id') ?? $request->input('category'))
                ->whenSearch($request->input('search'))
                ->orderBy($sort, $order)
                ->paginate($perPage);
        });

        return ProductResource::collection($products)->additional([
            'message' => 'Products retrieved successfully'
        ]);
    }

    public function lowStock(Request $request): AnonymousResourceCollection
    {
        [$sort, $order, $perPage] = $this->resolveSortingParams($request);

        $products = Product::with('category')
            ->active()
            ->whenCategory($request->input('category_id') ?? $request->input('category'))
            ->whenSearch($request->input('search'))
            ->lowStock()
            ->orderBy($sort, $order)
            ->paginate($perPage);

        return ProductResource::collection($products)
            ->additional(['message' => 'Low stock products retrieved successfully']);
    }

    private function resolveSortingParams(Request $request)
    {
        $sort = $request->filled('sort') && $request->sort !== 'null' ? $request->sort : 'id';
        $order = $request->filled('order') && $request->order !== 'null' ? $request->order : 'desc';

        return [$sort, $order, $request->integer('per_page', 15)];
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
            'image'       => 'nullable|file|mimes:jpeg,png,jpg|max:2048'
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
            'image'       => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
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
