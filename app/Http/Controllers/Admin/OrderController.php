<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\User;

use App\Models\Order;
use App\Models\SalePayment;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Imports\SaleCreateImport;
use App\DataTables\OrderDataTable;
use App\Jobs\OrderStatusChangeJob;
use App\Http\Requests\OrderRequest;
// use App\DataTables\ScanOutSaleDataTable;
use App\Mail\OrderStatusChangeMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;



class OrderController extends Controller
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
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Order'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Order'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit Order'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete Order'), only: ['destroy']),
            new Middleware(PermissionMiddleware::using('Make Payment Order'), only: ['salePayment','orderPaymentCreate']),
            new Middleware(PermissionMiddleware::using('Cancel Order'), only: ['orderCancel','orderCancelUpdate']),
            new Middleware(PermissionMiddleware::using('Delete Payment Order'), only: ['orderPaymentDelete']),
            new Middleware(PermissionMiddleware::using('Status Change Order'), only: ['orderStatusChange','orderStatusUpdate']),
            new Middleware(PermissionMiddleware::using('Histories Order'), only: ['orderHistories']),
            // new Middleware(PermissionMiddleware::using('Restore Administration'), only: ['restore']),
        ];
    }


    public function index(OrderDataTable $dataTable)
    {
        setPageMeta('Order List');
        setCreateRoute(route('orders.create'),'route');

        return $dataTable->render('admin.order.index');
    }

    /**
     * create
     *
     * @return void
     */
    public function create(Request $request): \Illuminate\View\View
    {
        checkPermission('Add Order');
        setPageMeta(__('Create Order'));
        $allActiveData = $this->orderService->getActiveAllData();
        $order_for = $request->order_for ?? 'Customer';
        setCreateRoute(null);
        $order_id = $request->get('id') ?? null;
        // dd($order_id);
        return view('admin.order.create',compact('order_for','order_id'),$allActiveData) ;
    }

    public function show($id){
        checkPermission('Show Order');
        setPageMeta('Show Order');
        setCreateRoute(null);

        $sale = $this->orderService->get($id)->load([
            'order_items',
            'customer',
            'order_payments',
            'order_items.product',
            'order_items.warehouse'
        ]);


        return view('admin.order.show',compact('sale'));

    }

    public function store(OrderRequest $request) : \Illuminate\Http\RedirectResponse
    {
        checkPermission('Add Order');
        $data = $request->validated();

        try {
            $sale = $this->orderService->createOrUpdate($request);

            sendFlash('Successfully created', 'success');
            return redirect()->route('orders.create', ['id' => $sale->id]);
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function edit(Request $request, $id) : \Illuminate\View\View
    {
        checkPermission('Edit Order');
        setPageMeta('Edit Order');
        setCreateRoute(null);

        $sale = $this->orderService->get($id)->load(['order_items','customer','order_payment','order_items.warehouse_stock','order_items.product']);


        $allActiveData = $this->orderService->getActiveAllData();

        $order_for = $request->order_for ?? $sale->order_for;

        return view('admin.order.edit',compact('sale','order_for'),$allActiveData) ;

    }

    public function salePayment($id){
        checkPermission('Make Payment Sale');
        setPageMeta('Make Payment');
        setCreateRoute(null);
        $sale = $this->orderService->get($id)->load(['order_items','location','outgoing_location','customer']);


        return view('admin.sale.payment',compact('sale')) ;

    }

    public function orderInvoiceDownload($id)
    {
        $sale = $this->orderService->get($id)->load([
            'order_items',
            'customer',
            'order_payments',
            'order_items.product',
            'order_items.warehouse'
        ]);

        //pdf display
        $pdf = PDF::loadView('admin.pdf.order.invoice-download', [
            'sale' => $sale
        ]);
        $pdf_name = 'Sale_Invoice_(' . $sale->invoice_no . ')_' . Carbon::now()->format('d-m-Y') . '.pdf';
        return $pdf->download($pdf_name);

        // return view('admin.pdf.order.invoice-download',compact('sale'));
    }

    public function orderPaymentCreate(Request $request, Order $sale){
        checkPermission('Make Payment Order');
        setCreateRoute(null);
        try {
            $this->orderService->paymentCreate($sale, $request->except('_token'));

            sendFlash('Successfully Payment Create');
            return redirect()->back();
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function update(OrderRequest $request, $id): RedirectResponse
    {
        checkPermission('Edit Order');
        $data = $request->validated();

        try {
            $this->orderService->createOrUpdate($request, $id);

            sendFlash('Successfully Updated');
            // return redirect()->route('orders.index');
            return redirect()->back();
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function destroy($id) : RedirectResponse
    {
        checkPermission('Delete Sale');
        try {
            $this->orderService->delete($id);
            sendFlash(__('Successfully Deleted'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }
    public function orderCancel($id)
    {
        checkPermission('Cancel Order');
        setPageMeta('Cancel Order');
        setCreateRoute(null);

        return view('admin.order.cancel',compact('id'));

    }
    public function orderCancelUpdate(Request $request, $id){
        checkPermission('Cancel Sale');
        try {
            $sale = $this->orderService->get($id) ;
            $sale->status = Sale::STATUS_CANCEL;
            $sale->cancel_date = $request->cancel_date;
            $sale->cancel_by = auth()->user()->id;
            $sale->cancel_note = $request->cancel_note;
            $sale->save();
            sendFlash(__('Successfully Cancelled'));
            return redirect()->route('orders.index');
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }

    }
    public function searchProduct(Request $request)
    {

        $ProductStocks =     $this->orderService->getProductFromStock( $request);
        // $searchItems =     $this->orderService->productStockFormat($ProductStocks);
        return response()->json($ProductStocks);
    }

    public function orderPaymentList(Request $request, $id){

        $sale = $this->orderService->get($id)->load(['order_payments']);

        if(isset($request->isPaginate) && $request->isPaginate){
            $html = view('admin.order.modal.transaction_list_table', compact('sale'))->render();
            return response()->json(['html' => $html]);
        }
        $html = view('admin.order.modal.transaction_list', compact('sale'))->render();
        return response()->json(['html' => $html]);
    }

    public function orderPaymentDelete($id, $pid){
        checkPermission('Delete Payment Order');

        try {
            $this->orderService->deleteOrderPayment($id, $pid);
            sendFlash(__('Successfully Payment Deleted'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }


    public function saleCreateImport(Request $request)
    {

        // Validate the request
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xls,xlsx|max:2048', // Max file size 2MB
            'location_id' => 'required|exists:locations,id',
            'outgoing_location_id' => 'required|exists:locations,id',
            'customer_id' => 'required|exists:users,id',
            'invoice_no' => 'required|unique:sales,invoice_no', // Unique validation
            'date' => 'required',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data = $request->except(['file','_token']);

        try {
            // Get the uploaded file
            $file = $request->file('file');

            // Import the file using the SaleCreateImport class
            Excel::import(new SaleCreateImport($data), $file);

            if(session('is_rollback') !== false){
                // Success message
                return redirect()->back()->with('success', 'Sales data imported successfully!');
            }
            return back();
        } catch (\Exception $e) {
            // Handle any exceptions during import
            return redirect()->back()
                ->with('error', 'An error occurred during the import: ' . $e->getMessage());
        }
    }
    public function orderPayment($id){
        checkPermission('Make Payment Sale');
        setPageMeta('Make Payment');
        setCreateRoute(null);

        $sale = $this->orderService->get($id)->load([
            'order_items',
            'customer',
            'order_payments',
            'order_items.product',
            'order_items.warehouse'
        ]);


        return view('admin.order.payment',compact('sale')) ;

    }

    public function orderPaymentEdit($id, $pid){

        setPageMeta('Edit Payment');
        setCreateRoute(null);

        $sale = $this->orderService->get($id)->load([
            'order_items',
            'customer',
            'order_payments',
            'order_items.product',
            'order_items.warehouse'
        ]);
        $salePayment = OrderPayment::findOrFail($pid);

        return view('admin.order.payment-edit',compact('sale','salePayment')) ;

    }

    public function orderPaymentUpdate(Request $request,Order $sale  , OrderPayment $salePayment){
        try {
            $this->orderService->paymentUpdate( $request, $sale, $salePayment);

            sendFlash('Successfully Payment Update');
            // return redirect()->route('orders.index');
            return redirect()->back();
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function orderStatusChange($id){
        checkPermission('Status Change Order');
        setPageMeta('Status Change Order');
        setCreateRoute(null);

        $sale = $this->orderService->get($id)->load([
            'order_items',
            'customer',
            'order_items.product',
            'order_items.warehouse',
            'order_statuses.user'
        ]);

        $deliveryMans = User::active()
        ->where('type',User::TYPE_DELIVERY_MAN)
        ->select('id','first_name')->get();

        return view('admin.order.status-change',compact('sale','deliveryMans'));

    }

    public function orderStatusUpdate(Request $request, $id){
        checkPermission('Status Change Order');
        $request->validate([
            'status' => 'required',
        ]);

        try {
            $this->orderService->statusUpdate($request,$id);

            sendFlash('Successfully Status Update', 'success');
            return redirect()->back();
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }

    }

    public function orderHistories($id){
        checkPermission('Histories Order');
        setPageMeta('Order Histories ');
        setCreateRoute(null);

        $sale = $this->orderService->get($id)->load([
            'order_statuses.user'
        ]);

        return view('admin.order.order-status-history',compact('sale'));

    }

    public function orderPrintDetails($id){
        $order = Order::query()
        ->with(['order_items','order_payments','customer'])
        ->find($id);
        $settings = [
            'site_logo' => config('settings.site_logo') ? getStorageImage(config('settings.site_logo'),false,'logo') : getDefaultLogo(),
            'wide_site_logo' => config('settings.wide_site_logo') ? getStorageImage(config('settings.wide_site_logo'),false,'wide_logo') : getDefaultWideLogo(),
            'site_title' => config('settings.site_title') ?? env('APP_NAME'),
        ];
        return response()->json([
            'success' => true,
            'order' => $order,
            'settings' => $settings
        ]);
    }


}
