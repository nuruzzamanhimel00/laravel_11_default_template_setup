<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\OrderNotifyEvent;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommonController;
use App\Notifications\EmailVerifyNotifyMail;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\WishListController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\DeliveryManController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\AdministrationController;
use App\Http\Controllers\Admin\DeliveryChargeController;
use App\Http\Controllers\Admin\PurchaseReturnController;
use App\Http\Controllers\Admin\Product\ProductController;
use App\Http\Controllers\Admin\PurchaseReceiveController;
use App\Http\Controllers\Admin\Warehouse\WarehouseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Route::redirect('/', 'login');

Route::middleware(['auth', 'verified'])->group(function (){
    Route::get('/dashboard',[DashboardController::class,'index'] )->name('dashboard');
    Route::resource('administrations',AdministrationController::class);
    Route::resource('roles', RoleController::class);

    Route::post('/subcategories/reorder', [CategoryController::class, 'reorderSubcategories'])->name('subcategories.reorder');
    Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

    Route::resource('categories',CategoryController::class);

    Route::resource('brands', BrandController::class);
    Route::resource('restaurants',RestaurantController::class);
    Route::get('restaurant-orders/{user}', [RestaurantController::class, 'restaurantOrders'])->name('restaurant.orders');
    Route::post('/update-status/{id}',[RestaurantController::class,'statusUpdate'])->name('restaurants.status.update');
    Route::resource('users',UserController::class);
    Route::get('user-orders/{user}', [UserController::class, 'userOrders'])->name('user.orders');
    Route::resource('delivery-mans',DeliveryManController::class);
    Route::resource('suppliers',SupplierController::class);
    Route::resource('promotions',PromotionController::class);
    Route::get('promotion-valid-product-category', [PromotionController::class, 'validProductCategory']);
    //purchase
    Route::resource('purchases',PurchaseController::class);
    Route::get('/search-product-for-purchase', [PurchaseController::class, 'searchProductForPurchase']);
    Route::get('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancelPurchase'])->name('purchases.cancel');
    Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancelPurchaseUpdate'])->name('purchases.cancel.update');
    //purchase receive
    Route::get('/purchases/receive/list', [PurchaseReceiveController::class, 'index'])->name('purchases.receive.index');
    Route::get('/purchases/{purchase}/receive', [PurchaseReceiveController::class, 'purchaseReceive'])->name('purchases.receive');
    Route::post('/purchases/{purchase}/receive', [PurchaseReceiveController::class, 'purchaseReceiveStore'])->name('purchases.receive.store');
    Route::delete('/purchases/receive/{id}/destroy', [PurchaseReceiveController::class, 'purchaseReceiveDestroy'])->name('purchases.receive.destroy');
    Route::get('/purchases-receive/{id}/show', [PurchaseReceiveController::class, 'purchaseReceiveShow'])->name('purchases.receive.show');
    //purchase return
    Route::get('/purchases/return/list', [PurchaseReturnController::class, 'index'])->name('purchases.return.index');
    Route::get('/purchases/{purchase}/return', [PurchaseReturnController::class, 'purchaseReturn'])->name('purchases.return');
    Route::post('/purchases/{purchase}/return', [PurchaseReturnController::class, 'purchaseReturnStore'])->name('purchases.return.store');
    Route::delete('/purchases/return/{id}/destroy', [PurchaseReturnController::class, 'purchaseReturnDestroy'])->name('purchases.return.destroy');
    Route::get('/purchases-return/{id}/show', [PurchaseReturnController::class, 'purchaseReturnShow'])->name('purchases.return.show');

    //order
    Route::resource('orders',OrderController::class);
    Route::get('/search-order-product', [OrderController::class, 'searchProduct']);
    Route::get('/order/{order}/invoice-download', [OrderController::class, 'orderInvoiceDownload'])->name('order.invoice.download');
    //order print label
    Route::get('/order/{id}/print-details', [OrderController::class, 'orderPrintDetails']);
    //cancel
    Route::get('/order/{id}/cancel', [OrderController::class, 'orderCancel'])->name('order.cancel');
    Route::post('/order/{id}/cancel', [OrderController::class, 'orderCancelUpdate'])->name('order.cancel.update');
    //order make payment
    Route::get('/order/{id}/payment', [OrderController::class, 'orderPayment'])->name('order.payment');
    Route::post('/order/{sale}/payment', [OrderController::class, 'orderPaymentCreate'])->name('order.payment.create');
    Route::get('/order/{id}/payment-list', [OrderController::class, 'orderPaymentList'])->name('order.payment-list');
    Route::get('/order/{id}/payment/{pid}', [OrderController::class, 'orderPaymentEdit'])->name('order.payment.edit');
    Route::post('/order/{sale}/payment/{salePayment}', [OrderController::class, 'orderPaymentUpdate'])->name('order.payment.update');
    Route::delete('/order/{id}/payment/{pid}/delete', [OrderController::class, 'orderPaymentDelete'])->name('order.payment.delete');
    //order status change
    Route::get('/order/{id}/status-change', [OrderController::class, 'orderStatusChange'])->name('order.status.change');
    Route::post('/order/{id}/status-update', [OrderController::class, 'orderStatusUpdate'])->name('order.status.update');
    Route::get('/order/{id}/histories', [OrderController::class, 'orderHistories'])->name('order.histories');
    // warehouse
    Route::resource('warehouses', WarehouseController::class);
    Route::resource('products', ProductController::class);
    Route::get('/download-product-barcode/{id}', [ProductController::class, 'downloadProductBarcode'])->name('product.barcode.download');
    Route::post('product/barcode-zip', [ProductController::class, 'barcodeDownloadZip'])->name('products.barcode.download.zip');
    Route::get('/product/{id}/reviews', [ProductController::class, 'productReviews'])->name('product.reviews');
    Route::get('/product-review/{rId}/edit', [ProductController::class, 'productReviewsEdit'])->name('product.reviews.edit');
    Route::put('/product-review/{rId}/update', [ProductController::class, 'productReviewsUpdate'])->name('product.reviews.update');
    Route::delete('/product-review/{rId}/delete', [ProductController::class, 'productReviewsDelete'])->name('product.reviews.delete');
    Route::get('/low-stock-products', [ProductController::class, 'lowStockProducts'])->name('product.low.stock');

    //notification
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notification-read/{id}', [NotificationController::class, 'read']);
    Route::get('/notification-read-all', [NotificationController::class, 'readAll']);

    //reports
    Route::prefix('reports')->group(function () {

        Route::get('/orders', [ReportController::class, 'orderReports'])->name('orders.reports.index');
        Route::get('/purchases', [ReportController::class, 'purchaseReports'])->name('purchases.reports.index');
        Route::get('/warehouse-stock', [ReportController::class, 'warehouseStockReports'])->name('warehouse-stock.reports.index');
    });
    //delivery charge
    Route::resource('delivery-charges', DeliveryChargeController::class);

    Route::resource('attributes',AttributeController::class);

    Route::resource('wish-lists',WishListController::class);

    Route::resource('settings', SettingController::class)->only(['index', 'store']);

    Route::post('users/bulk-delete', [UserController::class, 'bulk_destroy'])->name('users.bulk-destroy');

    Route::get('/notify', function(){
        // dd('dd');
        $notify = auth()->user()->notifications->last();
        broadcast(new OrderNotifyEvent($notify));
        dd($notify);
    });

//    Route::post('users/bulk-status-change', [UserController::class, 'bulk_status_change'])->name('users.bulk-status-change');


});



// Email Verification
Route::get('account/verify/{token}', [RegisteredUserController::class, 'verifyAccount'])->name('user.verify');
Route::get('email-verification', [RegisteredUserController::class, 'emailVerification'])->name('email.verification');

// Route::get('thank-you', function(){
//     // return redirect()->route('thank-you-blade', ['success' => 'Your email has been verified!']);
//     return redirect()->route('thank.you.confirmation', ['error' => 'Invalid or expired verification link.']);

//     // C:\laragon\www\grozaar\resources\views\frontend\thankyou.blade.php
// });

Route::get('thank-you-confirmation', function(){
    return view('frontend.thankyou')->with([
        'success' => request()->query('success'),
        'error' => request()->query('error')
    ]);
})->name('thank.you.confirmation');



//SYSTEM LOG
Route::get('app-logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

require __DIR__.'/auth.php';
