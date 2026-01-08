<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of addresses.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Address::with('customer');
        
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        $addresses = $query->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'data' => $addresses,
        ]);
    }

    /**
     * Store a newly created address.
     */
    public function store(StoreAddressRequest $request): JsonResponse
    {
        $address = Address::create($request->validated());
        $address->load('customer');
        
        return response()->json([
            'status' => 'success',
            'message' => 'Address created successfully',
            'data' => $address,
        ], 201);
    }

    /**
     * Display the specified address.
     */
    public function show(Address $address): JsonResponse
    {
        $address->load('customer');
        
        return response()->json([
            'status' => 'success',
            'data' => $address,
        ]);
    }

    /**
     * Update the specified address.
     */
    public function update(UpdateAddressRequest $request, Address $address): JsonResponse
    {
        $address->update($request->validated());
        $address->load('customer');
        
        return response()->json([
            'status' => 'success',
            'message' => 'Address updated successfully',
            'data' => $address,
        ]);
    }

    /**
     * Delete the specified address.
     */
    public function destroy(Address $address): JsonResponse
    {
        $address->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Address deleted successfully',
        ]);
    }
}
