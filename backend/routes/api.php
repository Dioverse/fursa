<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\ShippingAddressController;
use App\Http\Controllers\Api\Admin\DistributorController;
use App\Http\Controllers\Api\Distributor\ProfileController;
use App\Http\Controllers\Api\Admin\DistributorApprovalController;
use App\Http\Controllers\Api\OrderController as DistCustOrderController;
use App\Http\Controllers\Api\ProductController as GeneralProductController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:sanctum')->group(function () {

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('distributors', DistributorController::class);
        Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'update']);
        Route::post('approve-distributor/{id}', [DistributorApprovalController::class, 'approve']);
        Route::post('reject-distributor/{id}', [DistributorApprovalController::class, 'reject']);
    });

    // Distributor-only routes
    Route::middleware('role:distributor')->group(function () {
        Route::get('my-profile', [ProfileController::class, 'show']);
        Route::put('my-profile', [ProfileController::class, 'update']);
        Route::apiResource('orders', OrderController::class)->only(['index', 'show']);
    });

    // Customer-Distributor routes
    Route::middleware('role:customer,distributor')->group(function () {
        Route::apiResource('orders', DistCustOrderController::class)->only(['store', 'index', 'show']);
        Route::apiResource('shipping-addresses', ShippingAddressController::class);
    });

    // Shared routes for all authenticated users
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::get('products', [GeneralProductController::class, 'index']);
Route::get('products/{id}', [GeneralProductController::class, 'show']);
Route::post('apply-discount', [GeneralProductController::class, 'apply']);