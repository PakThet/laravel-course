<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserRoleController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\PermissionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth routes
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::get('users/profile', [UserController::class, 'getUserProfile'])->middleware('auth:sanctum');
Route::patch('users/profile', [UserController::class, 'updateProfile'])->middleware('auth:sanctum');
Route::put('users/change-password', [UserController::class, 'changePassword'])->middleware('auth:sanctum');

// Users
Route::middleware('auth:sanctum')->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
    Route::get('roles', [UserController::class, 'getRoles']);

    // User Roles
    Route::post('users/{user}/assign-roles', [UserRoleController::class, 'assignRoles']);
    Route::post('users/{user}/revoke-roles', [UserRoleController::class, 'revokeRoles']);
    Route::get('users/{user}/roles', [UserRoleController::class, 'getUserRoles']);
});

// Roles
Route::middleware('auth:sanctum')->group(function () {
    Route::get('roles', [RoleController::class, 'index']);
    Route::post('roles', [RoleController::class, 'store']);
    Route::get('roles/{role}', [RoleController::class, 'show']);
    Route::put('roles/{role}', [RoleController::class, 'update']);
    Route::delete('roles/{role}', [RoleController::class, 'destroy']);

    // Role Permissions
    Route::post('roles/{role}/assign-permissions', [RoleController::class, 'assignPermissions']);
    Route::post('roles/{role}/revoke-permissions', [RoleController::class, 'revokePermissions']);
});

// Permissions
Route::middleware('auth:sanctum')->group(function () {
    Route::get('permissions', [PermissionController::class, 'index']);
    Route::post('permissions', [PermissionController::class, 'store']);
    Route::get('permissions/{permission}', [PermissionController::class, 'show']);
    Route::put('permissions/{permission}', [PermissionController::class, 'update']);
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy']);
});
