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


});

