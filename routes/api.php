<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\HomepageController;
use App\Http\Controllers\Api\V1\Cart\CartController;
use App\Http\Controllers\Api\V1\Test\TestController;
use App\Http\Controllers\Api\V1\Brand\BrandController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Review\ReviewController;
use App\Http\Controllers\Api\V1\DeliveryChargeController;
use App\Http\Controllers\Api\V1\Product\ProductController;
use App\Http\Controllers\Api\V1\Profile\ProfileController;
use App\Http\Controllers\Api\V1\Category\CategoryController;
use App\Http\Controllers\Api\V1\Checkout\CheckoutController;
use App\Http\Controllers\Api\V1\Wishlist\WishlistController;
use App\Http\Controllers\Api\V1\Promotion\PromotionController;
use App\Http\Controllers\Api\V1\Order\User\UserOrderController;
use App\Http\Controllers\Api\V1\Notification\NotificationController;
use App\Http\Controllers\Api\V1\Order\DeliveryMan\OrderController as DeliveryManOrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::fallback(function () {
//     // return
//     return response()->json([
//         'status' => false,
//         'message' => 'API resource not found',
//         'data' => []
//     ], 404);
// });
// Route::prefix('v1')->middleware('api')->group(function () {
Route::prefix('v1')->group(function () {

    // Route::middleware('auth:passport')->get('/user', function (Request $request) {
    Route::middleware(['auth:api', 'auth.passport'])->get('/user', function (Request $request) {
        return $request->user();
    });


    Route::middleware(['auth:api', 'auth.passport'])->group(function () {
        Route::post('logout', [RegisterController::class, 'logout']);

        //---------------- restaurant and regular user router ---------------------
        Route::prefix('user')->middleware(['is_user'])->group(function () {
            //------------- Profile ----------------------------
            Route::get('/get-profile-details', [ProfileController::class, "show"]);
            Route::put('/update-profile', [ProfileController::class, "update"]);

            //------------- checkout --------------------------
            Route::post('/checkout-confirm', [CheckoutController::class, "checkoutConfirm"]);
            //------------- Order ----------------------------
            Route::post('/order/{id}/status-change', [UserOrderController::class, "statusChange"]);
            Route::get('/ongoing-orders', [UserOrderController::class, "ongoingOrders"]);
            Route::get('/completed-orders', [UserOrderController::class, "completedOrders"]);
            Route::get('/order/{id}/details', [UserOrderController::class, "show"]);
            Route::get('/user-orders', [UserOrderController::class, "userOrders"]);
            Route::get('/get-order-payment-summery', [UserOrderController::class, "orderPaymentSummery"]);
            //------------- review --------------------------
            Route::post('/product-reviews/create', [ReviewController::class, "store"]);
            // Route::get('/product/{id}/reviews', [ReviewController::class, "index"]);
            Route::put('/product-reviews/{rId}/update', [ReviewController::class, "update"]);
            Route::delete('/product-reviews/{rId}/delete', [ReviewController::class, "destroy"]);
            Route::get('/product/{id}/can-review', [ReviewController::class, "canReview"]);
            // //------------- Cart --------------------------
            // Route::get('/cart', [CartController::class, 'getCart']);
            // Route::post('/cart/plus', [CartController::class, 'addToCart']);
            // Route::post('/cart/minus', [CartController::class, 'minusToCart']);
            // Route::delete('/cart/{id}/remove', [CartController::class, 'removeFromCart']);
        });
        //-------------------- Delivery Man router ---------------------
        Route::prefix('delivery-man')->middleware(['is_delivery_man'])->group(function () {
            //------------- Profile ----------------------------
            Route::get('/get-profile-details', [ProfileController::class, "show"]);
            Route::put('/update-profile', [ProfileController::class, "update"]);
            //------------- category --------------------------
            Route::get('get-notifications', [NotificationController::class, "index"]);
            Route::get('read-notification/{id}', [NotificationController::class, "read"]);
            Route::get('/notification-read-all', [NotificationController::class, 'readAll']);

            //-------------------- Orders ---------------------
            Route::get('/get-accepted-orders', [DeliveryManOrderController::class, "index"]);
            Route::get('/order/{id}/details', [DeliveryManOrderController::class, "show"]);
            Route::get('/order-histories/{id}', [DeliveryManOrderController::class, "orderHistories"]);
            Route::post('/order/{id}/status-change', [DeliveryManOrderController::class, "statusChange"]);
            Route::get('/get-order-request', [DeliveryManOrderController::class, "orderRequest"]);
            Route::get('/get-current-orders', [DeliveryManOrderController::class, "getCurrentOrders"]);
            Route::get('/get-previous-orders', [DeliveryManOrderController::class, "getPreviousOrders"]);
        });
    });

    //global routes
    Route::prefix('user')->group(function () {
        //------------- category --------------------------
        Route::get('get-parent-categories', [CategoryController::class, "getParentCategories"]);
        Route::get('get-categories', [CategoryController::class, "index"]);
        Route::get('/get-category-details/{slug}', [CategoryController::class, "show"]);
        Route::get('/category/{id}/sub-categories', [CategoryController::class, "getSubCategories"]);
        //------------- brand --------------------------
        Route::get('get-brands', [BrandController::class, "index"]);
        Route::get('/get-brand-details/{id}', [BrandController::class, "show"]);
        //------------- product --------------------------
        Route::get('get-products', [ProductController::class, "index"]);
        Route::get('/get-product-details/{id}', [ProductController::class, "show"]);
        Route::get('/product/{id}/related-items', [ProductController::class, "getRelatedItems"]);
        Route::get('/best-selling-products', [ProductController::class, "bestSellingProducts"]);
        Route::get('/new-arrival-products', [ProductController::class, "newArrivalProducts"]);
        //------------- review --------------------------
        Route::get('/product/{id}/reviews', [ReviewController::class, "getProductReviews"]);
        // ------------ Promotions -------------------------
        Route::get('get-promotions', [PromotionController::class, "index"]);
        // ------------ Home -------------------------
        Route::get('get-homepage', [HomepageController::class, "index"]);
        // ------------ Delivery Change -------------------------
        Route::get('get-delivery-charges', [DeliveryChargeController::class, "index"]);
        Route::get('get-delivery-charge', [DeliveryChargeController::class, "getDeliveryCharge"]);

        //------------- Cart --------------------------
        Route::get('/cart', [CartController::class, 'getCart']);
        Route::post('/cart/plus', [CartController::class, 'addToCart']);
        Route::post('/cart/minus', [CartController::class, 'minusToCart']);
        Route::delete('/cart/{id}/remove', [CartController::class, 'removeFromCart']);
        //------------- Wishlist --------------------------
        Route::get('/wishlist', [WishlistController::class, 'wishList']);
        Route::post('/wishlist/add', [WishlistController::class, 'addToWishlist']);
        Route::post('/wishlist/remove', [WishlistController::class, 'removeToWishlist']);
    });
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('login', [RegisterController::class, 'login']);
    Route::post('refresh-token', [RegisterController::class, 'refreshToken']);
    Route::post('email-verification', [RegisterController::class, 'emailVerification']);
    Route::post('send-otp-again', [RegisterController::class, 'sendOTPToEmailAgain']);
    //forget password
    Route::post('forget-password', [RegisterController::class, 'forgetPassword']);
    Route::post('forget-password/otp-verify', [RegisterController::class, 'forgetPasswordOtpVerify']);
    Route::post('forget-password/otp-generate-again', [RegisterController::class, 'forgetPasswordOtpAgainGenerate']);
    Route::post('forget-password/update', [RegisterController::class, 'forgetPasswordOtpUpdate']);



    Route::get('/test', [TestController::class, "index"]);
});

