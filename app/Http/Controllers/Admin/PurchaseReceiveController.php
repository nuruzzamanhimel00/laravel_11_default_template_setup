<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Location;

use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\WarehouseStock;
use App\Services\InvestorService;
use App\Services\PurchaseService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Storage;
use App\Services\PurchaseReceiveService;
use App\DataTables\PurchaseReceiveDataTable;
use App\Http\Requests\PurchaseReceiveRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;



class PurchaseReceiveController extends Controller
{
    protected $purchaseService;
    protected $purchaseReceiveService;




    public function __construct(PurchaseService $purchaseService, PurchaseReceiveService $purchaseReceiveService)
    {
        $this->purchaseService = $purchaseService;
        $this->purchaseReceiveService = $purchaseReceiveService;

    }

    /**
     * Define middleware for the controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Receive Purchase'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Receive Purchase'), only: ['purchaseReceive','purchaseReceiveStore']),
            new Middleware(PermissionMiddleware::using('Show Receive Purchase'), only: ['purchaseReceiveShow']),
            new Middleware(PermissionMiddleware::using('Delete Receive Purchase'), only: ['purchaseReceiveDestroy']),
        ];
    }



    public function index(PurchaseReceiveDataTable $dataTable)
    {
        checkPermission('List Receive Purchase');
        setPageMeta('Purchase Receive List');

        setCreateRoute(null);

        return $dataTable->render('admin.purchase_receive.index');
    }
    public function purchaseReceiveShow($id){
        setPageMeta('Show Receive Purchase');

        $purchaseReceive = $this->purchaseReceiveService->get($id)->load(['purchase_receive_items.product','purchase']);
        $itemList = [];
        foreach ($purchaseReceive->purchase_receive_items as $purItem) {
            $product = $purItem->product;
            if ($product->is_variant) {
                // // Fetch product variant with attributes
                // $productVariant = ProductVariant::with(['product_attribute_values'])
                //     ->find($purItem->product_variant_id);

                // if ($productVariant) {
                //     $itemList[] = $this->formatPurReceiveItem($purItem, $product, $productVariant);
                // }
            } else {
                // Handle non-variant products
                $itemList[] = $this->formatPurReceiveItem($purItem, $product);
            }

        }
        return view('admin.purchase_receive.show',compact('purchaseReceive','itemList'));

    }

    private function formatPurReceiveItem($purItem, $product, $productVariant = null)
    {
        return [

            'product_name' =>  $product->name,
            'product_sku' => $product->sku,
            'quantity' => $purItem->quantity,
            'price' => $purItem->price,
            'sub_total' => $purItem->sub_total,
            'barcode' => $productVariant ? $productVariant->barcode : $product->barcode,
            'size'  => $productVariant->name ?? null,
            // 'size'  => $productVariant->product_attribute_values->pluck('value')->implode(', ') ?? null,
            'condition_name' => $productVariant ? $productVariant->condition->name : $product->condition->name ?? '',
            'name' =>    $product->name

        ];
    }

    public function purchaseReceive($id){
        checkPermission('Add Receive Purchase');
        setPageMeta('Receive Purchase');
        setCreateRoute(null);
        $purchase = $this->purchaseService->get($id)->load(['supplier','warehouse','purchase_items.purchase_receive_items','purchase_items.product']);

        $purchaseItems = $this->purchaseReceiveItemList($purchase);


        return view('admin.purchase_receive.receive',compact('purchase','purchaseItems','id'));
    }

    public function purchaseReceiveItemList($purchase){
        $purchaseItems = [];

        foreach ($purchase->purchase_items as $purItem) {
            $product = $purItem->product;

            if ($product->is_variant) {
                // Fetch product variant with attributes
                // $productVariant = ProductVariant::with(['product_attribute_values'])
                //     ->find($purItem->product_variant_id);

                // if ($productVariant) {
                //     $purchaseItems[] = $this->formatProductItem($purItem, $product, $productVariant);
                // }
            } else {
                // Handle non-variant products
                $purchaseItems[] = $this->formatProductItem($purItem, $product);
            }
        }
        return $purchaseItems;
    }

    private function formatProductItem($purItem, $product, $productVariant = null)
    {
        $warehouse_Stock = WarehouseStock::where('warehouse_id', $purItem->warehouse_id)
        ->when(!is_null($productVariant), function($query) use ($productVariant) {
            $query->where('product_variant_id', $productVariant->id);
        })
        ->where('product_id', $purItem->product_id)->first();
        return [
            'product_id' => $purItem->product_id,
            'purchase_item_id' => $purItem->id,
            'warehouse_id' =>$purItem->warehouse_id,
            'product_variant_id' => $productVariant ? $purItem->product_variant_id : null,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'purchase_quantity' => $purItem->quantity,
            'purchase_price' => (int)$purItem->price,
            'purchase_notes' => $purItem->notes,
            'purchase_sub_total' => (int)$purItem->sub_total,
            'total_receive_quantity' => (int)$purItem->purchase_receive_items->sum('quantity') ?? 0,
            'total_receive_price' => (int)$purItem->purchase_receive_items->sum('price') ?? 0,
            'total_receive_sub_total' => (int)$purItem->purchase_receive_items->sum('sub_total') ?? 0,
            'receive_quantity' => '',
            'receive_price' => (int)$purItem->price,
            'receive_sale_price' => (int)$purItem->sale_price,
            'receive_restaurant_sale_price' => (int)$purItem->restaurant_sale_price,
            // 'receive_price' => isset($warehouse_Stock) ? $warehouse_Stock->purchase_price :  $purItem->price,
            'receive_sub_total' => '',
            'can_purchase_quantity' => ($purItem->quantity - $purItem->purchase_receive_items->sum('quantity') ?? 0),
            'barcode' => $productVariant ? $productVariant->barcode : $product->barcode,
            'size'  => $productVariant->name ?? null,
            'condition_name' =>  '',
            'name' =>    $product->name,
        ];
    }


    // private function generateProductName($product, $productVariant = null)
    // {
    //     $baseName = '(SKU: ' . $product->sku . ') ' . $product->name;

    //     if ($productVariant) {
    //         // $variantAttributes = $productVariant->product_attribute_values->pluck('value')->implode(', ');
    //         // $variantAttributes =  ' (Size:' . $productVariant->product_attribute_values->pluck('value')->implode(', ') . ')';
    //         $variantAttributes =  ' (Size:' . $productVariant->name . ')';
    //         return $baseName . $variantAttributes ;
    //     }

    //     return $baseName;
    // }


    public function purchaseReceiveStore(PurchaseReceiveRequest $request, $id){
        checkPermission('Add Receive Purchase');
        $data = $request->validated();

        try {
            $this->purchaseReceiveService->create($request, $id);

            // Forget the cache for the key 'system_settings'
            Cache::forget('system_settings');
            sendFlash('Successfully Received');
            return redirect()->route('purchases.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }

    }

    public function purchaseReceiveDestroy($id){
        checkPermission('Delete Receive Purchase');
        try {
            $this->purchaseReceiveService->delete($id);
            sendFlash(message: __('Successfully Deleted'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }

}
