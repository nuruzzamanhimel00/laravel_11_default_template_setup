<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\User;

use App\Models\Order;
use App\Models\SalePayment;
use App\Models\Notification;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Imports\SaleCreateImport;
use App\DataTables\OrderDataTable;
use App\Jobs\OrderStatusChangeJob;
// use App\DataTables\ScanOutSaleDataTable;
use App\Http\Requests\OrderRequest;
use App\Mail\OrderStatusChangeMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;



class NotificationController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;


    }

    /**
     * Define middleware for the controller.
     *
     * @return array
     */
    // public static function middleware(): array
    // {
    //     return [
    //         new Middleware(PermissionMiddleware::using('List Order'), only: ['index']),

    //     ];
    // }





    public function index(Request $request)
    {
        $notifications = Notification::getPaginateData();

        return response()->json($notifications);
    }

    public function read($id)
    {
        $notification = Notification::get($id);

        $notification->markAsRead();

        return response()->json($notification);
    }
    public function readAll()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(true);
    }


}
