<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Review::with(['product', 'customer']);
        
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }
        
        $reviews = $query->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'data' => $reviews,
        ]);
    }

    /**
     * Store a newly created review.
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $review = Review::create($request->validated());
        $review->load(['product', 'customer']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Review created successfully',
            'data' => $review,
        ], 201);
    }

    /**
     * Display the specified review.
     */
    public function show(Review $review): JsonResponse
    {
        $review->load(['product', 'customer']);
        
        return response()->json([
            'status' => 'success',
            'data' => $review,
        ]);
    }

    /**
     * Update the specified review.
     */
    public function update(UpdateReviewRequest $request, Review $review): JsonResponse
    {
        $review->update($request->validated());
        $review->load(['product', 'customer']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Review updated successfully',
            'data' => $review,
        ]);
    }

    /**
     * Delete the specified review.
     */
    public function destroy(Review $review): JsonResponse
    {
        $review->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Review deleted successfully',
        ]);
    }
}
