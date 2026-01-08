<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'brand', 'images', 'variants']);
        
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        $products = $query->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        $product->load(['category', 'brand', 'images', 'variants']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'brand', 'images', 'variants', 'reviews']);
        
        return response()->json([
            'status' => 'success',
            'data' => $product,
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());
        $product->load(['category', 'brand', 'images', 'variants']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    /**
     * Delete the specified product.
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully',
        ]);
    }
}
