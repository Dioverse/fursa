<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\ContentController;
use App\Http\Controllers\Api\Admin\PaymentController;
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

Route::post('register', [AuthController::class, 'register']);
Route::get('login', function () {
    return response()->json([
        'message' => "Unauthenticated"
    ], 401);
})->name("login");
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::get('reset-password/{token}', function (string $token, Request $request) {
    
    $frontendUrl = config('app.frontend_url').'/reset-password';

    return redirect()->away($frontendUrl . '?token=' . $token . '&email=' . $request->email);
})->name('password.reset');

Route::middleware(['auth:sanctum','ban'])->group(function () {
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'emailVerify'])->middleware('signed')->name('verification.verify');
    // Resend verification email
    Route::post('/email/verification-notification', [AuthController::class, 'verificationSend'])->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware(['auth:sanctum','ban', 'verified'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::post('users/update-status/{id}', [UserController::class, 'updateStatus']);
        
        Route::apiResource('content', ContentController::class);


        Route::apiResource('admin', AdminController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('admin-products', ProductController::class);
        Route::apiResource('distributors', DistributorController::class);

        Route::apiResource('admin-orders', OrderController::class)->only(['index', 'show']);
        Route::post('admin-orders/update-status/{id}', [OrderController::class, 'updateStatus']);

        Route::apiResource('admin-payments', PaymentController::class)->only(['index', 'show']);
        Route::post('admin-payments/update-status/{id}', [PaymentController::class, 'updateStatus']);
    });

    // Distributor-only routes
    Route::middleware('role:distributor')->prefix('distributor')->group(function () {
        Route::get('my-profile', [ProfileController::class, 'show']);
        Route::put('my-profile', [ProfileController::class, 'update']);
    });

    // Customer & Distributor routes
    Route::middleware('role:customer,distributor')->group(function () {
        Route::apiResource('orders', DistCustOrderController::class)->only(['index', 'show']);
        Route::apiResource('shipping-addresses', ShippingAddressController::class);
    });

    // Shared routes for all authenticated users
});

Route::get('products', [GeneralProductController::class, 'index']);
Route::get('products/{id}', [GeneralProductController::class, 'show']);
Route::post('apply-discount', [GeneralProductController::class, 'apply']);