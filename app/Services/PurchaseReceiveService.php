<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\Location;
use App\Models\Purchase;
use Illuminate\Support\Str;
use App\Models\ProductStock;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\WarehouseStock;
use App\Models\PurchaseReceive;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseReceiveItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PurchaseReceiveService extends BaseService
{
//    protected $fileUploadService;

    public function __construct(PurchaseReceive $model)
    {
        parent::__construct($model);
    }
    public function create($request, $id)
    {
        try {
            DB::beginTransaction();

            $purchase = Purchase::findOrFail($id);
            $purchaseReceive = $this->createPurchaseReceive($purchase, $request);

            $purchaseReceiveItems = [];
            $totalReceiveQty = 0;

            foreach ($request->purchase_receive as $item) {
                if ($this->isValidReceiveItem($item)) {
                    $purchaseReceiveItems[] = $this->prepareReceiveItem($purchaseReceive->id, $item);
                    $totalReceiveQty += $item['receive_quantity'];

                    $this->updateProductStock($item);
                    $this->updateWarehouseStock($item);

                    PurchaseItem::where('id', $item['purchase_item_id'])
                        ->increment('receive_quantity', $item['receive_quantity']);

                }
            }

            if (!empty($purchaseReceiveItems)) {
                $purchaseReceive->purchase_receive_items()->createMany($purchaseReceiveItems);
                $purchase->update(['status' => Purchase::STATUS_RECEIVED]);
                DB::commit();
            } else {
                DB::rollBack();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    private function createPurchaseReceive($purchase, $request)
    {
        return $purchase->purchase_receive()->create(
            $request->only(['receive_date', 'total', 'warehouse_id'])
        );
    }

    private function isValidReceiveItem($item)
    {
        return isset($item['receive_quantity']) && $item['receive_quantity'] > 0;
    }

    private function prepareReceiveItem($purchaseReceiveId, $item)
    {
        return [
            'purchase_receive_id' => $purchaseReceiveId,
            'purchase_item_id' => $item['purchase_item_id'],
            'product_id' => $item['product_id'],
            'product_variant_id' => $item['product_variant_id'] ?? null,
            'warehouse_id' => $item['warehouse_id'] ?? null,
            'quantity' => $item['receive_quantity'],
            'price' => (int)$item['receive_price'],
            'sale_price' => (int)$item['receive_sale_price'],
            'restaurant_sale_price' => (int)$item['receive_restaurant_sale_price'],
            'sub_total' => (int)$item['receive_sub_total'],
        ];
    }

    private function updateProductStock($item)
    {
        // $prices = calPurchaseSaleAndRestaurantPrices($item['receive_price']);
        $product = Product::findOrFail($item['product_id']);
        $product->update([
            'total_stock_quantity' => $product->total_stock_quantity + $item['receive_quantity'],
            'purchase_price' => $item['receive_price'],
            'sale_price' => $item['receive_sale_price'],
            'restaurant_sale_price' => $item['receive_restaurant_sale_price'],
        ]);
        // $product->increment('total_stock_quantity', $item['receive_quantity']);
    }

    private function updateWarehouseStock($item)
    {
        $ProductStock = WarehouseStock::query()
            ->where('product_id', $item['product_id'])
            ->where('warehouse_id', $item['warehouse_id'])
            ->when(isset($item['product_variant_id']), function ($query) use ($item) {
                return $query->where('product_variant_id', $item['product_variant_id']);
            })
            ->first();

        if ($ProductStock) {
            $this->updateExistingWarehouseStock($ProductStock, $item);
            $ProductStock->received_at = Carbon::now();
            $ProductStock->save();
        } else {
            $this->createNewWarehouseStock($item);
        }

    }

    private function updateExistingWarehouseStock($ProductStock, $item)
    {
        // $prices = calPurchaseSaleAndRestaurantPrices($item['receive_price']);
        $ProductStock->increment('stock_quantity', $item['receive_quantity']);
        // $ProductStock->update([
        //     'purchase_price' => $prices['default_price'],
        //     'sale_price' => $prices['sale_price'],
        //     'restaurant_sale_price' => $prices['restaurant_price'],
        // ]);
    }

    private function createNewWarehouseStock($item)
    {
        $prices = calPurchaseSaleAndRestaurantPrices($item['receive_price']);
        WarehouseStock::create([
            'product_id' => $item['product_id'],
            'product_variant_id' => $item['product_variant_id'] ?? null,
            'warehouse_id' => $item['warehouse_id'] ?? null,
            'stock_quantity' => $item['receive_quantity'],
            'purchase_price' =>null,
            'sale_price' => null,
            'restaurant_sale_price' => null,
            'received_at' => Carbon::now(),

        ]);
    }




    // public function calculateAveragePurchasePrice(array $purchaseReceives): void
    // {
    //     if (empty($purchaseReceives)) {
    //         return;
    //     }

    //     foreach ($purchaseReceives as $item) {
    //         $receiveItems = PurchaseReceiveItem::query()
    //             ->when(!empty($item['product_variant_id']), function ($query) use ($item) {
    //                 return $query->where('product_variant_id', $item['product_variant_id']);
    //             })
    //             ->where('product_id', $item['product_id'])
    //             ->whereHas('purchase_receive', function($query) use($item){
    //                 $query->where('location_id', $item['location_id']);
    //             })
    //             ->select(['quantity', 'sub_total','product_id','product_variant_id','id'])
    //             ->get();

    //         if ($receiveItems->isNotEmpty()) {
    //             $totalQuantity = $receiveItems->sum('quantity');
    //             $totalSubTotal = $receiveItems->sum('sub_total');

    //             if ($totalQuantity > 0) {
    //                 $avgPrice = $totalSubTotal / $totalQuantity;

    //                 ProductStock::query()
    //                     ->where('product_id', $item['product_id'])
    //                     ->when(isset($item['product_variant_id']) &&!empty($item['product_variant_id']), function ($query) use ($item) {
    //                         return $query->where('variant_id', $item['product_variant_id']);
    //                     })
    //                     ->where('warehouse_id',  $item['location_id'])
    //                     ->update(['avg_purchase_price' => $avgPrice]);
    //             }
    //         }
    //     }
    // }

    public function delete($id){
        $purchaseReceive = $this->get($id);
        $purchaseReceive->purchase_receive_items()->delete();
        $purchaseReceive->delete();
    }

}
