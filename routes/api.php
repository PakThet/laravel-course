<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\UserRolePermissionController;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Route Model Bindings
Route::model('role', Role::class);
Route::model('permission', Permission::class);

// Public Authentication Endpoints
Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/verify-email', [AuthController::class, 'verifyEmail']);
});

// Public endpoints
Route::prefix('v1')->group(function () {
    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    // Brands
    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/brands/{brand}', [BrandController::class, 'show']);

    // Reviews
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::get('/reviews/{review}', [ReviewController::class, 'show']);

    // Customers
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{customer}', [CustomerController::class, 'show']);
});

// Protected endpoints (require Sanctum authentication)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/logout-all', [AuthController::class, 'logoutAll']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

    // User endpoints
    Route::get('/me', [UserController::class, 'me']);
    
    // User management - requires admin role
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });

    // User Roles and Permissions - requires admin role
    Route::middleware('role:admin')->group(function () {
        Route::get('/users/{user}/roles', [UserRolePermissionController::class, 'getRoles']);
        Route::get('/users/{user}/permissions', [UserRolePermissionController::class, 'getPermissions']);
        Route::post('/users/{user}/assign-role', [UserRolePermissionController::class, 'assignRole']);
        Route::post('/users/{user}/revoke-role', [UserRolePermissionController::class, 'revokeRole']);
        Route::post('/users/{user}/sync-roles', [UserRolePermissionController::class, 'syncRoles']);
        Route::post('/users/{user}/check-permission', [UserRolePermissionController::class, 'hasPermission']);
        Route::post('/users/{user}/check-role', [UserRolePermissionController::class, 'hasRole']);
    });

    // Role management - requires admin role
    Route::middleware('role:admin')->group(function () {
        Route::get('/roles', [RoleController::class, 'index']);
        Route::post('/roles', [RoleController::class, 'store']);
        Route::get('/roles/{role}', [RoleController::class, 'show']);
        Route::put('/roles/{role}', [RoleController::class, 'update']);
        Route::delete('/roles/{role}', [RoleController::class, 'destroy']);
        Route::post('/roles/{role}/assign-permission', [RoleController::class, 'assignPermission']);
        Route::post('/roles/{role}/revoke-permission', [RoleController::class, 'revokePermission']);
    });

    // Permission management - requires admin role
    Route::middleware('role:admin')->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index']);
        Route::post('/permissions', [PermissionController::class, 'store']);
        Route::get('/permissions/{permission}', [PermissionController::class, 'show']);
        Route::put('/permissions/{permission}', [PermissionController::class, 'update']);
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy']);
    });

    // Product management - requires create_products, edit_products, delete_products permissions
    Route::post('/products', [ProductController::class, 'store'])->middleware('permission:create_products');
    Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('permission:edit_products');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('permission:delete_products');

    // Category management - requires create_categories, edit_categories, delete_categories permissions
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('permission:create_categories');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->middleware('permission:edit_categories');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->middleware('permission:delete_categories');

    // Brand management - requires create_brands, edit_brands, delete_brands permissions
    Route::post('/brands', [BrandController::class, 'store'])->middleware('permission:create_brands');
    Route::put('/brands/{brand}', [BrandController::class, 'update'])->middleware('permission:edit_brands');
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->middleware('permission:delete_brands');

    // Order endpoints
    Route::get('/orders', [OrderController::class, 'index'])->middleware('permission:view_orders');
    Route::post('/orders', [OrderController::class, 'store'])->middleware('permission:manage_orders');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->middleware('permission:view_orders');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->middleware('permission:manage_orders');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->middleware('permission:manage_orders');

    // Customer management - requires create_customers, edit_customers, delete_customers permissions
    Route::post('/customers', [CustomerController::class, 'store'])->middleware('permission:create_customers');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->middleware('permission:edit_customers');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->middleware('permission:delete_customers');

    // Review management - requires create_reviews, edit_reviews, delete_reviews permissions
    Route::post('/reviews', [ReviewController::class, 'store'])->middleware('permission:create_reviews');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->middleware('permission:edit_reviews');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->middleware('permission:delete_reviews');

    // Coupon management - requires manage_coupons permission
    Route::middleware('permission:manage_coupons')->group(function () {
        Route::get('/coupons', [CouponController::class, 'index']);
        Route::post('/coupons', [CouponController::class, 'store']);
        Route::get('/coupons/{coupon}', [CouponController::class, 'show']);
        Route::put('/coupons/{coupon}', [CouponController::class, 'update']);
        Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy']);
    });

    // Address management - requires manage_addresses permission
    Route::middleware('permission:manage_addresses')->group(function () {
        Route::get('/addresses', [AddressController::class, 'index']);
        Route::post('/addresses', [AddressController::class, 'store']);
        Route::get('/addresses/{address}', [AddressController::class, 'show']);
        Route::put('/addresses/{address}', [AddressController::class, 'update']);
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);
    });
});
