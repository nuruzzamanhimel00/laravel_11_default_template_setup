<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Services\PurchaseService;
use App\Http\Controllers\Controller;
use App\DataTables\PurchaseReturnDataTable;

use App\Services\PurchaseReturnService;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PurchaseReturnRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;



class PurchaseReturnController extends Controller
{
    protected $purchaseService;
    protected $purchaseReturnService;



    public function __construct(PurchaseService $purchaseService, PurchaseReturnService $purchaseReturnService)
    {
        $this->purchaseService = $purchaseService;
        $this->purchaseReturnService = $purchaseReturnService;
    }

    /**
     * Define middleware for the controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Return Purchase'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Return Purchase'), only: ['purchaseReturn', 'purchaseReturnStore']),
            new Middleware(PermissionMiddleware::using('Show Return Purchase'), only: ['purchaseReturnShow']),
            new Middleware(PermissionMiddleware::using('Delete Return Purchase'), only: ['purchaseReturnDestroy']),
            // new Middleware(PermissionMiddleware::using('Restore Administration'), only: ['restore']),
        ];
    }


    public function index(PurchaseReturnDataTable $dataTable)
    {
        setPageMeta('List Return Purchase');
        setCreateRoute(null);
        return $dataTable->render('admin.purchase_return.index');
    }
    public function purchaseReturnShow($id){
        setPageMeta('Show Return Purchase');
        setCreateRoute(null);
        $purchaseReturn = $this->purchaseReturnService->get($id)->load(['purchase_return_items.product','purchase']);
        $itemList = [];
        foreach ($purchaseReturn->purchase_return_items as $purItem) {
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
        // dd($itemList);
        return view('admin.purchase_return.show',compact('purchaseReturn','itemList'));

    }

    private function formatPurReceiveItem($purItem, $product, $productVariant = null)
    {
        return [

            'product_name' => $this->generateProductName($product, $productVariant),
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

    public function purchaseReturn($id){

        setPageMeta(content: 'Return Purchase');
        $purchase = $this->purchaseService->get($id)->load(['supplier','warehouse','purchase_items.purchase_receive_items','purchase_items.product']);
        $purchaseItems = [];

        foreach ($purchase->purchase_items as $purItem) {
            $product = $purItem->product;

            if ($product->is_variant) {
                // // Fetch product variant with attributes
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
        // dd($purchaseItems);
        return view('admin.purchase_return.return',compact('purchase','purchaseItems','id'));
    }

    private function formatProductItem($purItem, $product, $productVariant = null)
    {
        return [
            'product_id' => $purItem->product_id,
            'purchase_item_id' => $purItem->id,
            'warehouse_id' => $purItem->warehouse_id,
            'product_variant_id' => $productVariant ? $purItem->product_variant_id : null,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'purchase_quantity' => $purItem->quantity,
            'purchase_price' =>  round($purItem->price,2) ?? 0,
            'purchase_notes' => $purItem->notes,
            'purchase_sub_total' => round($purItem->sub_total,2) ?? 0,

            'total_receive_quantity' => $purItem->purchase_receive_items->sum('quantity') ?? 0,
            'total_receive_price' => round($purItem->purchase_receive_items->sum('price'),2)  ?? $purItem->purchase_receive_items->sum('price') ?? 0,
            'total_receive_sub_total' => round($purItem->purchase_receive_items->sum('sub_total'),2) ??  $purItem->purchase_receive_items->sum('sub_total') ?? 0,

            'total_return_quantity' => $purItem->purchase_return_items->sum('quantity') ?? 0,

            'return_quantity' => '',
            'return_price' => $purItem->price,
            'return_sub_total' => '',
            'can_return_quantity' => ( $purItem->purchase_receive_items->sum('quantity') ?? 0) - ( $purItem->purchase_return_items->sum('quantity') ?? 0),
            'barcode' => $productVariant ? $productVariant->barcode : $product->barcode,
            'size'  => $productVariant->sku ?? null,
            // 'size'  => $productVariant->product_attribute_values->pluck('value')->implode(', ') ?? null,
            'condition_name' => null,
            'name' =>    $product->name
        ];
    }
    private function generateProductName($product, $productVariant = null)
    {
        $baseName = '(SKU: ' . $product->sku . ') ' . $product->name;

        if ($productVariant) {
            // $variantAttributes = $productVariant->product_attribute_values->pluck('value')->implode(', ');
            // $variantAttributes =  ' (Size:' . $productVariant->product_attribute_values->pluck('value')->implode(', ') . ')';
            $variantAttributes =  ' (Size:' . $productVariant->sku . ')';
            return $baseName . $variantAttributes ;
        }

        return $baseName;
    }

    // private function generateProductName($product, $productVariant = null)
    // {
    //     $baseName = '(SKU: ' . $product->sku . ') ' . $product->name;

    //     if ($productVariant) {
    //         $variantAttributes = $productVariant->product_attribute_values->pluck('value')->implode(', ');
    //         return $baseName . ' (' . $variantAttributes . ')';
    //     }

    //     return $baseName;
    // }

    public function purchaseReturnStore(PurchaseReturnRequest $request, $id){
        $data = $request->validated();

        try {
            $this->purchaseReturnService->create($request, $id);
            sendFlash('Successfully Return');
            return redirect()->route('purchases.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }

    }


    public function purchaseReturnDestroy($id){

        try {
            $this->purchaseReturnService->delete($id);
            sendFlash(message: __('Successfully Deleted'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }

}
