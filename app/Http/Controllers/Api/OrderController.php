<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['customer', 'items.product', 'statusHistories', 'coupon']);
        
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }
        
        $orders = $query->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }

    /**
     * Store a newly created order.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $items = $validated['items'] ?? [];
        unset($validated['items']);

        // Calculate discount if coupon is provided
        if ($validated['coupon_id'] ?? null) {
            $coupon = Coupon::find($validated['coupon_id']);
            if ($coupon) {
                $discount = $coupon->calculateDiscount($validated['subtotal']);
                $validated['discount_amount'] = $discount;
            }
        }

        $order = Order::create($validated);

        // Create order items if provided
        if (!empty($items)) {
            foreach ($items as $item) {
                // Get product details
                $product = Product::find($item['product_id']);
                if ($product) {
                    $item['product_name'] = $product->name;
                    $item['product_sku'] = $product->sku;
                }
                // Calculate subtotal
                $item['subtotal'] = $item['quantity'] * $item['price'];
                $order->items()->create($item);
            }
        }

        $order->load(['customer', 'items.product', 'statusHistories', 'coupon']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Order created successfully',
            'data' => $order,
        ], 201);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['customer', 'items.product', 'statusHistories', 'coupon']);
        
        return response()->json([
            'status' => 'success',
            'data' => $order,
        ]);
    }

    /**
     * Update the specified order.
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $order->update($request->validated());
        $order->load(['customer', 'items.product', 'statusHistories', 'coupon']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Order updated successfully',
            'data' => $order,
        ]);
    }

    /**
     * Delete the specified order.
     */
    public function destroy(Order $order): JsonResponse
    {
        $order->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Order deleted successfully',
        ]);
    }
}
