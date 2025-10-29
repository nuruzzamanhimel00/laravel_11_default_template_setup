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


    Route::resource('restaurants',RestaurantController::class);
    Route::get('restaurant-orders/{user}', [RestaurantController::class, 'restaurantOrders'])->name('restaurant.orders');
    Route::post('/update-status/{id}',[RestaurantController::class,'statusUpdate'])->name('restaurants.status.update');
    Route::resource('users',UserController::class);
    Route::get('user-orders/{user}', [UserController::class, 'userOrders'])->name('user.orders');


    Route::resource('settings', SettingController::class)->only(['index', 'store']);

    Route::post('users/bulk-delete', [UserController::class, 'bulk_destroy'])->name('users.bulk-destroy');

    // Route::get('/notify', function(){
    //     // dd('dd');
    //     $notify = auth()->user()->notifications->last();
    //     broadcast(new OrderNotifyEvent($notify));
    //     dd($notify);
    // });



});




//SYSTEM LOG
Route::get('app-logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

require __DIR__.'/auth.php';
