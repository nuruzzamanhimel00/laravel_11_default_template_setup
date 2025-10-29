<?php


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\Admin\SettingController;

use App\Http\Controllers\Admin\DashboardController;

use App\Http\Controllers\Admin\AgentController;

use App\Http\Controllers\Admin\AdministrationController;


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


    Route::resource('agents',AgentController::class);

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
