<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of coupons.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Coupon::withCount('usages');
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        $coupons = $query->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'data' => $coupons,
        ]);
    }

    /**
     * Store a newly created coupon.
     */
    public function store(StoreCouponRequest $request): JsonResponse
    {
        $coupon = Coupon::create($request->validated());
        $coupon->loadCount('usages');
        
        return response()->json([
            'status' => 'success',
            'message' => 'Coupon created successfully',
            'data' => $coupon,
        ], 201);
    }

    /**
     * Display the specified coupon.
     */
    public function show(Coupon $coupon): JsonResponse
    {
        $coupon->loadCount('usages');
        
        return response()->json([
            'status' => 'success',
            'data' => $coupon,
        ]);
    }

    /**
     * Update the specified coupon.
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon): JsonResponse
    {
        $coupon->update($request->validated());
        $coupon->loadCount('usages');
        
        return response()->json([
            'status' => 'success',
            'message' => 'Coupon updated successfully',
            'data' => $coupon,
        ]);
    }

    /**
     * Delete the specified coupon.
     */
    public function destroy(Coupon $coupon): JsonResponse
    {
        $coupon->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Coupon deleted successfully',
        ]);
    }
}
