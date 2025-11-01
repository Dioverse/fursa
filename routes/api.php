<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\Admin\PostController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\ContentController;
use App\Http\Controllers\Api\Admin\PaymentController;
use App\Http\Controllers\Api\Admin\ProductController;
// use App\Http\Controllers\Api\Admin\LanguageController;
// use App\Http\Controllers\Api\Admin\SettingsController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\ShippingController;

use App\Http\Controllers\Api\Admin\InventoryController;
use App\Http\Controllers\Api\ShippingAddressController;
use App\Http\Controllers\Api\Admin\DistributorController;
use App\Http\Controllers\Api\Admin\NotificationController;
use App\Http\Controllers\Api\Admin\PostCategoryController;
use App\Http\Controllers\Api\Distributor\ProfileController;
use App\Http\Controllers\Api\PostController as DistCustPostController;
// use App\Http\Controllers\PaymentController as DistCustPaymentController;
use App\Http\Controllers\Api\OrderController as DistCustOrderController;
use App\Http\Controllers\Api\ProductController as GeneralProductController;
use App\Http\Controllers\Api\LanguageController as DistCustLanguageController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::get('login', function () {
    return response()->json(['redirect' => "unauthenticated",'message' => "Unauthenticated"], 401);
})->name("login");
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::get('auth/google', [OAuthController::class, 'loginOrRegister']);
// Route::get('auth/google/callback', [OAuthController::class, 'handleProviderCallback']);
Route::middleware('auth:api')->post('/auth/admin/refresh', [AuthController::class, 'refresh']);

// Route::get('reset-password/{token}', function (string $token, Request $request) {
//     $frontendUrl = config('app.frontend_url').'/reset-password';
//     return redirect()->away($frontendUrl . '?token=' . $token . '&email=' . $request->email);
// })->name('password.reset');

Route::middleware(['auth:sanctum','ban'])->group(function () {
    // Resend verification email
    Route::post('/email/verification-notification', [AuthController::class, 'verificationSend'])->middleware('throttle:2,1')->name('verification.send');
});

Route::middleware(['auth:sanctum','ban', 'verifiedcustom'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::post('users/{id}/change-password', [UserController::class, 'changePassword']);
        Route::post('users/toggle-ban/{id}', [UserController::class, 'toggleBan']);
        
        Route::apiResource('content', ContentController::class);


        Route::apiResource('admin', AdminController::class);
        Route::get('auth/admin/profile-details', function (Request $request) {
            return $request->user();
        });

        Route::get('admin-dashboard', [AdminController::class, 'dashboard']);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('admin-products', ProductController::class);
        Route::post('admin-products/{id}/images', [ProductController::class, 'addImages']);
        Route::delete('admin-products/{id}/images', [ProductController::class, 'deleteImages']);
        Route::post('admin-products-toggle/{id}', [ProductController::class, 'toggleStatus']);
        Route::post('admin-products-bulk-action', [InventoryController::class, 'bulkAction']);
        Route::post('admin-products-stock/{id}', [InventoryController::class, 'stock']);
        Route::get('admin-inventory-logs', [InventoryController::class, 'index']);
        Route::get('admin-inventory-logs/{id}', [InventoryController::class, 'show']);
        Route::get('distributors', [DistributorController::class, 'index']);
        Route::patch('distributors/{id}/status', [DistributorController::class, 'update']);

        Route::apiResource('admin-orders', OrderController::class)->only(['index', 'show']);
        Route::post('admin-orders/update-status/{id}', [OrderController::class, 'updateStatus']);

        Route::apiResource('admin-shippings', ShippingController::class);
        Route::post('admin-shippings-bulk-action', [ShippingController::class, 'bulkAction']);


        Route::apiResource('admin-payments', PaymentController::class)->only(['index', 'show']);
        Route::post('admin-payments/update-status/{id}', [PaymentController::class, 'updateStatus']);

        Route::apiResource('admin-posts', PostController::class);
        Route::apiResource('admin-post-categories', PostCategoryController::class);
        // Route::post('admin-language', [LanguageController::class, 'store']);
        // Route::post('admin-language-update/{name}', [LanguageController::class, 'update']);




        // Dashboard & Statistics
        Route::get('admin/settings/notifications', [NotificationController::class, 'index']);
        Route::get('admin/settings/notifications/statistics', [NotificationController::class, 'statistics']);
        Route::get('admin/settings/notifications/queue-status', [NotificationController::class, 'queueStatus']);

        // // Logs Management
        // Route::get('admin/settings/notifications/logs', [NotificationController::class, 'logs']);
        // Route::delete('admin/settings/notifications/logs', [NotificationController::class, 'clearLogs']);
        // Route::get('admin/settings/notifications/logs/export', [NotificationController::class, 'exportLogs']);
        // Route::post('admin/settings/notifications/logs/retry', [NotificationController::class, 'retryFailed']);

        // Templates
        // Route::get('admin/settings/notifications/templates', [NotificationController::class, 'templates']);
        // Route::post('admin/settings/notifications/templates', [NotificationController::class, 'templateCreate']);
        // Route::get('admin/settings/notifications/templates/{id}', [NotificationController::class, 'templateEdit']);
        // Route::put('admin/settings/notifications/templates/{id}', [NotificationController::class, 'templateUpdate']);
        // Route::delete('admin/settings/notifications/templates/{id}', [NotificationController::class, 'templateDelete']);
        // Route::post('admin/settings/notifications/templates/validate', [NotificationController::class, 'validateTemplate']);

        // // Testing & Sending
        // Route::post('admin/settings/notifications/test', [NotificationController::class, 'sendTest']);
        // Route::post('admin/settings/notifications/bulk', [NotificationController::class, 'sendBulk']);

        // Configuration
        // Route::get('admin/settings/notifications/email', [NotificationController::class, 'emailSetting']);
        // Route::put('admin/settings/notifications/email', [NotificationController::class, 'emailSettingUpdate']);
        // Route::post('admin/settings/notifications/email/test', [NotificationController::class, 'emailTest']);
        // Route::put('admin/settings/notifications/email/global', [NotificationController::class, 'globalEmailUpdate']);


        Route::post('admin/email/global-update', [NotificationController::class, 'globalEmailUpdate']);
        Route::get('admin/email/templates', [NotificationController::class, 'templates']);
        Route::post('admin/email/template-update/{id}', [NotificationController::class, 'templateUpdate']);
        Route::get('admin/email/setting', [NotificationController::class, 'emailSetting']);
        Route::post('admin/email/setting-update', [NotificationController::class, 'emailSettingUpdate']);



        Route::get('admin/site/info', [AdminController::class, 'siteSettings']);
        Route::put('admin/site/name', [AdminController::class, 'siteNameUpdate']);
        Route::put('admin/site/logo', [AdminController::class, 'siteLogoUpdate']);
        Route::put('admin/site/tax', [AdminController::class, 'taxUpdate']);

        // Payment settings
        Route::get('admin/site/gateways', [AdminController::class, 'paymentSettings']);
        Route::post('admin/site/gateway/paystack', [AdminController::class, 'updatePaystack']);
        Route::post('admin/site/gateway/flutterwave', [AdminController::class, 'updateFlutterwave']);
        


    });

    // Distributor-only routes
    Route::prefix('distributor')->middleware('role:distributor')->group(function () {
        Route::get('profile-details', [ProfileController::class, 'show']);
        Route::post('profile-update', [ProfileController::class, 'update'])->middleware('ensurerejected');
        Route::post('profile-document-upload', [ProfileController::class, 'updateDocuments'])->middleware('ensurerejected');
    });

    // Customer & Distributor routes
    Route::middleware('role:customer,distributor')->group(function () {
        Route::get('dashboard', [GeneralController::class, 'dashboard']);
        Route::apiResource('orders', DistCustOrderController::class)->only(['index', 'show', 'update']);
        Route::apiResource('shipping-address', ShippingAddressController::class);
        Route::post('set-default-address/{id}', [ShippingAddressController::class, 'setDefaultAddress']);
    });
    Route::post('distributor-pplication', [AuthController::class, 'upgradeToDistributor']);
    // Route::middleware('role:customer')->group(function () {
    // });

    Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('throttle:2,1');
    
    // Shared routes for all authenticated users
    Route::apiResource('carts', CartController::class)->only(['index','store']);
    Route::delete('carts/rm', [CartController::class, 'unSetItem']);
    Route::post('carts/clear', [CartController::class, 'unSetAllItem']);
    Route::patch('carts/update', [CartController::class, 'updateItemQuantity']);
    Route::post('checkout/init', [CheckoutController::class, 'initialize']);
    Route::post('place-order/{gateway}', [CheckoutController::class, 'makeOrder']);
    Route::post('checkout/{gateway}/{transId}/{orderId}', [CheckoutController::class, 'checkout']);

    Route::apiResource('wishlists', WishlistController::class)->only(['index','store']);
    Route::delete('wishlists/rm', [WishlistController::class, 'unSetItem']);

});

Route::get('products', [GeneralProductController::class, 'index']);
Route::get('products/{id}', [GeneralProductController::class, 'show']);
Route::get('shop', [GeneralProductController::class, 'shop']);
Route::get('cats', [GeneralProductController::class, 'cats']);

Route::post('apply-coupon', [GeneralProductController::class, 'apply']);

Route::get('posts', [DistCustPostController::class, 'index']);
Route::get('posts/{slug}', [DistCustPostController::class, 'show']);
Route::get('blog-categories', [DistCustPostController::class, 'getBlogCategories']);

Route::get('/lang/fetch/{lang}/{qry}', [DistCustLanguageController::class, 'fetch']);

// Email verification
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'emailVerify'])->middleware('signed')->name('verification.verify');

Route::get('states-provinces/{country}', [ShippingAddressController::class, 'getStatesWithProvinces']);
Route::get('countries', [ShippingAddressController::class, 'getCountries']);



// Route::get('/test-queue-setup', function () {
//     return response()->json([
//         'laravel_version' => app()->version(),
//         'queue_connection' => config('queue.default'),
//         'database_connection' => config('database.default'),
//         'jobs_table_exists' => Schema::hasTable('jobs'),
//         'failed_jobs_table_exists' => Schema::hasTable('failed_jobs'),
//         'notification_service_bound' => app()->bound('notify'),
//         'pending_jobs' => DB::table('jobs')->count(),
//         'failed_jobs' => DB::table('failed_jobs')->count(),
//     ]);
// });
Route::get('/test-notification/{email}/{queue?}', function ($email, $queue) {
    Illuminate\Support\Facades\Cache::forget("GeneralSetting");

    $user = new App\Models\User();
    $user->first_name = 'Ark';
    $user->last_name = "lar";
    $user->id = 1;
    $user->email = $email;
    $user->phone = "08152397199";
    notify(
        templateName: 'TEST_TEMPLATE',
        user: $user,
        shortCodes: [
            'subject' => 'System Update',
            'test_message' => 'Hello World',
            'user_name' => 'Quadri'
        ],
        sendVia: ['email'],
        queue: $queue ? true : false
    );

    return response()->json([
        'message' => 'Test notification sent/queued successfully',
        'queue_connection' => config('queue.default'),
        'email' => $email,
        'queue' => $queue
    ]);
});

