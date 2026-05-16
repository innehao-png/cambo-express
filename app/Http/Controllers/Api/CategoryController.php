<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\IsAdmin;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use IsAdmin;
    public function index(Request $request)
    {
        $categories = Category::where('status', 'active')
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return response()->json([
            'message' => 'Categories retrieved successfully',
            'data' => $categories,
        ]);
    }

    // Get single category with products
    public function show($id)
    {
        $category = Category::with('products')->find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Category retrieved successfully',
            'data' => $category,
        ]);
    }

    // Create category (admin only)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|unique:categories,name',
            'slug'   => 'required|string|unique:categories,slug',
        ]);

        $category = Category::create([
            ...$validated,
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    // Update category (admin only)
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
            ], 404);
        }

        $validated = $request->validate([
            'name'   => 'string|unique:categories,name,' . $id,
            'slug'   => 'string|unique:categories,slug,' . $id,
            'status' => 'in:active,inactive',
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    // Delete category (admin only)
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }
}
