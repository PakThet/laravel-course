<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Customer::with(['addresses', 'orders']);
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        $customers = $query->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'data' => $customers,
        ]);
    }

    /**
     * Store a newly created customer.
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());
        $customer->load(['addresses', 'orders']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Customer created successfully',
            'data' => $customer,
        ], 201);
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer): JsonResponse
    {
        $customer->load(['addresses', 'orders', 'reviews']);
        
        return response()->json([
            'status' => 'success',
            'data' => $customer,
        ]);
    }

    /**
     * Update the specified customer.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer->update($request->validated());
        $customer->load(['addresses', 'orders']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Customer updated successfully',
            'data' => $customer,
        ]);
    }

    /**
     * Delete the specified customer.
     */
    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Customer deleted successfully',
        ]);
    }
}
