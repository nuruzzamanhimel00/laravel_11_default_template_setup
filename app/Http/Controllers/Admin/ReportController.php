<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Services\BrandService;
use App\DataTables\BrandDataTable;
use App\Http\Requests\BrandRequest;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class ReportController extends Controller
{
    protected $brand_service;
    public function __construct(BrandService $brand_service)
    {
        $this->brand_service   = $brand_service;
    }

    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('Sale Report'), only: ['saleReports']),

        ];
    }


    public function orderReports(Request $request)
    {
        setPageMeta('Sales Report');
        setCreateRoute(null);

        $from_date = $request->query('from_date') ?? Carbon::now()->startOfMonth()->toDateString();
        $to_date = $request->query('to_date') ?? Carbon::now()->endOfMonth()->toDateString();
        $all_time = $request->query('q') ?? null;


        $orders = Order::query()
            ->when($all_time == null, function ($query) use($from_date, $to_date) {
                return $query->whereDate('date', '>=', $from_date)
                ->whereDate('date', '<=', $to_date);
            })

            ->latest()
            ->with(['customer'])
            ->get();


        return view('admin.report.sales-report', compact('orders', 'from_date', 'to_date','all_time'));
    }
    public function purchaseReports(Request $request)
    {
        setPageMeta('Purchases Report');
        setCreateRoute(null);

        $from_date = $request->query('from_date') ?? Carbon::now()->startOfMonth()->toDateString();
        $to_date = $request->query('to_date') ?? Carbon::now()->endOfMonth()->toDateString();
        $all_time = $request->query('q') ?? null;


        $purchases = Purchase::query()
            ->when($all_time == null, function ($query) use($from_date, $to_date) {
                return $query->whereDate('date', '>=', $from_date)
                ->whereDate('date', '<=', $to_date);
            })

            ->latest()
            ->with([
                'supplier'=> function($query){
                    $query->select(['users.id','users.first_name','users.email','users.email','users.phone','users.type']);
                },
                'supplier.supplier:id,user_id,company',
                'purchase_items.purchase_receive_items',
                'purchase_items.purchase_return_items'
            ])
            ->get();

        return view('admin.report.purchase-report', compact('purchases', 'from_date', 'to_date','all_time'));
    }

    public function warehouseStockReports(Request $request)
    {
        setPageMeta('Warehouse Stock Report');
        setCreateRoute(null);

        $products = Product::latest()
        ->with(['category','brand','warehouse_stock'])
        ->get();

        return view('admin.report.warehouse-stock-report', compact('products'));
    }



}
