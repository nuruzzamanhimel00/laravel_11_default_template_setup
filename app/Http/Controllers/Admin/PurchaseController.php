<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Location;

use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Services\PurchaseService;
use App\Http\Controllers\Controller;
use App\DataTables\PurchaseDataTable;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Storage;
use App\DataTables\ScanInPurchaseDataTable;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;



class PurchaseController extends Controller
{
    protected $purchaseService;
    protected $categoryService;
    protected $brandService;


    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;


    }

    /**
     * Define middleware for the controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Purchase'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Purchase'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit Purchase'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete Purchase'), only: ['destroy']),
            // new Middleware(PermissionMiddleware::using('Restore Administration'), only: ['restore']),
        ];
    }


    public function index(PurchaseDataTable $dataTable)
    {
        setPageMeta('Purchase List');

        setCreateRoute(route('purchases.create'),'route');

        return $dataTable->render('admin.purchase.index');
    }

    /**
     * create
     *
     * @return void
     */
    public function create(Request $request): \Illuminate\View\View
    {
        checkPermission('Add Purchase');
        setPageMeta(__('Add Purchase'));

        setCreateRoute(null);
        $data = $this->purchaseService->getActiveAllData();
        $warehouse = isset($data['warehouses']) ? $data['warehouses']->where('is_default',1)->first() : null;

        // dd($data);

        $warehouse_id = isset($request->warehouse_id) ? $request->warehouse_id :   $warehouse->id ?? null;


        return view('admin.purchase.create', compact('warehouse_id'),$data);
    }

    public function show($id){
        checkPermission('Show Purchase');
        setCreateRoute(null);

        setPageMeta(__('Show Purchase'));
        $purchase = $this->purchaseService->get($id)->load(['supplier','warehouse','purchase_items.product','purchase_receives']);

        return view('admin.purchase.show',compact('purchase'));

    }

    public function store(PurchaseRequest $request) : \Illuminate\Http\RedirectResponse
    {
        checkPermission('Add Purchase');
        $data = $request->validated();

        try {
            $this->purchaseService->createOrUpdate($request);

            sendFlash('Successfully created', 'success');
            return redirect()->route('purchases.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function edit($id) : \Illuminate\View\View
    {
        checkPermission('Edit Purchase');
        setPageMeta('Edit Purchase');
        setCreateRoute(null);

        $purchase = $this->purchaseService->get($id)->load(['supplier','warehouse','purchase_items.product']);

        $purchase_items = $this->purchaseService->getPurchaseProductItems($purchase);
        // dd($purchase_items);


        return view('admin.purchase.edit', compact('purchase','purchase_items'),$this->purchaseService->getActiveAllData());
    }


    public function update(PurchaseRequest $request, $id): RedirectResponse
    {
        checkPermission('Edit Purchase');
        $data = $request->validated();
        // dd('udt',$data);
        try {
            $this->purchaseService->createOrUpdate($request, $id);

            sendFlash('Successfully Updated');
            return redirect()->route('purchases.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function destroy($id) : RedirectResponse
    {
        checkPermission('Delete Purchase');
        try {
            $this->purchaseService->delete($id);
            sendFlash(__('Successfully Deleted'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }
    public function cancelPurchase($id)
    {
        checkPermission('Cancel Purchase');
        setPageMeta('Cancel Purchase');
        setCreateRoute(null);

        return view('admin.purchase.cancel',compact('id'));

    }
    public function cancelPurchaseUpdate(Request $request, $id){
        checkPermission('Cancel Purchase');
        try {
            $Purchase = Purchase::find($id);
            $Purchase->status = Purchase::STATUS_CANCEL;
            $Purchase->cancel_date = $request->cancel_date;
            $Purchase->cancel_note = $request->cancel_note;
            $Purchase->save();
            sendFlash(__('Successfully Cancelled'));
            return redirect()->route('purchases.index');
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }

    }

    public function searchProductForPurchase(Request $request)
    {
        $search = $request->search;

        if (empty($search)) {
            return response()->json([], 200);
        }

        $products = Product::query()
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('sku', $search)
                    ->orWhere('barcode', $search);
            })
            // ->with(['warehouse_stock'])
            ->active()
            ->get();

        $searchItems = $products->map(function ($product) {
            $purchasePrice = $product->purchase_price ?? 0;
            $sale_price = $product->sale_price ?? 0;
            $restaurant_sale_price = $product->restaurant_sale_price ?? 0;

            return [
                'product_id' => $product->id,
                'product_variant_id' => null,
                'product_name' => '(SKU: ' . $product->sku . ') ' . $product->name,
                'product_sku' => $product->sku,
                'quantity' => 1,
                'price' => $purchasePrice,
                'sale_price' => $sale_price,
                'restaurant_sale_price' => $restaurant_sale_price,
                'notes' => '',
                'sub_total' => $purchasePrice,
                'barcode' => $product->barcode,
                'size'  => null,
                'condition_name'  => null,
                'name' => $product->name,
                'avg_price' => $purchasePrice
            ];
        });

        return response()->json($searchItems, 200);
    }

}
