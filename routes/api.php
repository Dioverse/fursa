<?php

use App\Http\Controllers\Api\Admin\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\Admin\LanguageController;
use App\Http\Controllers\Api\Admin\PostController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\ContentController;
use App\Http\Controllers\Api\Admin\PaymentController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\SettingsController;
use App\Http\Controllers\Api\ShippingAddressController;
use App\Http\Controllers\Api\Admin\DistributorController;
use App\Http\Controllers\Api\Admin\PostCategoryController;
use App\Http\Controllers\Api\Distributor\ProfileController;
use App\Http\Controllers\Api\LanguageController as DistCustLanguageController;
use App\Http\Controllers\Api\PostController as DistCustPostController;
use App\Http\Controllers\Api\PaymentController as ApiPaymentController;
use App\Http\Controllers\Api\OrderController as DistCustOrderController;
use App\Http\Controllers\PaymentController as DistCustPaymentController;
use App\Http\Controllers\Api\ProductController as GeneralProductController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::get('login', function () {
    return response()->json([
        'redirect' => "unauthenticated",
        'message' => "Unauthenticated"
    ], 401);
})->name("login");
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Route::get('reset-password/{token}', function (string $token, Request $request) {
//     $frontendUrl = config('app.frontend_url').'/reset-password';
//     return redirect()->away($frontendUrl . '?token=' . $token . '&email=' . $request->email);
// })->name('password.reset');

Route::middleware(['auth:sanctum','ban'])->group(function () {
    // Resend verification email
    Route::post('/email/verification-notification', [AuthController::class, 'verificationSend'])->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware(['auth:sanctum','ban', 'verified'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::post('users/{id}/change-password', [UserController::class, 'changePassword']);
        Route::post('users/toggle-ban/{id}', [UserController::class, 'toggleBan']);
        
        Route::apiResource('content', ContentController::class);


        Route::apiResource('admin', AdminController::class);
        Route::get('admin-dashboard', [AdminController::class, 'dashboard']);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('admin-products', ProductController::class);
        Route::post('admin-products/{id}/images', [ProductController::class, 'addImages']);
        Route::delete('admin-products/{id}/images', [ProductController::class, 'deleteImages']);
        Route::post('admin-products-toggle/{id}', [ProductController::class, 'toggleStatus']);
        Route::post('admin-products-stock', [ProductController::class, 'stock']);
        Route::get('distributors', [DistributorController::class, 'index']);
        Route::patch('distributors/{id}/status', [DistributorController::class, 'update']);

        Route::apiResource('admin-orders', OrderController::class)->only(['index', 'show']);
        Route::post('admin-orders/update-status/{id}', [OrderController::class, 'updateStatus']);

        Route::apiResource('admin-payments', PaymentController::class)->only(['index', 'show']);
        Route::post('admin-payments/update-status/{id}', [PaymentController::class, 'updateStatus']);

        Route::apiResource('admin-posts', PostController::class);
        Route::apiResource('admin-post-categories', PostCategoryController::class);
        // Route::post('admin-language', [LanguageController::class, 'store']);
        // Route::post('admin-language-update/{name}', [LanguageController::class, 'update']);


        Route::get('admin/settings/notifications/email', [NotificationController::class, 'emailSetting']);
        Route::post('admin/settings/notifications/email', [NotificationController::class, 'emailSettingUpdate']);





        // Route::get('admin-settings', [SettingsController::class, 'index']);
        // Route::get('admin-settings/{key}', [SettingsController::class, 'show']);
        // Route::post('admin-settings', [SettingsController::class, 'update']);
    });

    // Distributor-only routes
    Route::middleware('role:distributor')->prefix('distributor')->group(function () {
    });

    // Customer & Distributor routes
    Route::middleware('role:customer,distributor')->group(function () {
        Route::get('dashboard', [GeneralController::class, 'dashboard']);
        Route::get('profile-details', [ProfileController::class, 'show']);
        Route::post('profile-update', [ProfileController::class, 'update']);
        Route::post('profile-document-upload', [ProfileController::class, 'updateDocuments']);
        Route::apiResource('orders', DistCustOrderController::class)->only(['index', 'show', 'update']);
        Route::apiResource('shipping-address', ShippingAddressController::class);
        Route::post('set-default-address/{id}', [ShippingAddressController::class, 'setDefaultAddress']);
    });

    // Shared routes for all authenticated users
    Route::apiResource('carts', CartController::class);
    Route::post('checkout/init', [ApiPaymentController::class, 'initialize']);
    Route::post('checkout/{gateway}/{transId}', [ApiPaymentController::class, 'checkout']);

});

Route::get('products', [GeneralProductController::class, 'index']);
Route::get('products/{id}', [GeneralProductController::class, 'show']);
Route::post('apply-discount', [GeneralProductController::class, 'apply']);

Route::get('posts', [DistCustPostController::class, 'index']);
Route::get('posts/{slug}', [DistCustPostController::class, 'show']);
Route::get('blog-categories', [DistCustPostController::class, 'getBlogCategories']);

Route::get('/lang/fetch/{lang}/{qry}', [DistCustLanguageController::class, 'fetch']);

// Email verification
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'emailVerify'])->middleware('signed')->name('verification.verify');



Route::prefix('test')->group(function () {
    Route::post('email', [NotificationController::class, 'emailTest']);
});