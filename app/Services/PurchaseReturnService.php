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
use App\Models\PurchaseReturn;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PurchaseReturnService extends BaseService
{
//    protected $fileUploadService;

    public function __construct(PurchaseReturn $model)
    {
        parent::__construct($model);
    }

    public function create($request, $id){
        $purchaseReturnData = $request->only(['return_date','total','note']);
        $purchaseReturnItems = [];
        $data = $request->all();

        $pRecItemFlg = 0;
        try {
            DB::beginTransaction();
            $purchase = Purchase::find($id);
            $purchaseReturn = $purchase->purchase_return()->create($purchaseReturnData);
            // dd($purchaseReturn);

            if(count($data['purchase_return']) > 0){
                foreach ($data['purchase_return'] as $item) {

                    if ($this->isValidItem($item)) {

                        $pRecItemFlg++;
                        $purchaseReturnItems[] = $this->prepareReturnItem($purchaseReturn->id, $item);

                        $this->updateProductStock($item);
                        $this->updateWarehouseStock($item);


                        PurchaseItem::where('id', $item['purchase_item_id'])
                        ->decrement('receive_quantity', $item['return_quantity']);
                    }
                }
            }

            if($pRecItemFlg > 0){
                $purchaseReturn->purchase_return_items()->createMany($purchaseReturnItems);
                DB::commit();
            }else{
                DB::rollBack();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

    }

    private function updateWarehouseStock($item)
    {
        $variant_id = $item['product_variant_id'] ?? null;
        $stock = WarehouseStock::query()
        ->where('product_id',$item['product_id'])
        ->where('warehouse_id',$item['warehouse_id'])
        ->when(!is_null($variant_id), function($query) use($variant_id){
            return $query->where('product_variant_id',$variant_id);
        })
        ->first();
        if($stock){
            $stock->stock_quantity -= $item['return_quantity'];
            $stock->save();
        }
    }
    private function updateProductStock($item)
    {
        $product = Product::findOrFail($item['product_id']);
        $product->decrement('total_stock_quantity', $item['return_quantity']);
    }
    private function prepareReturnItem($purchaseReturnId, $item){
        return [
            'purchase_return_id' => $purchaseReturnId,
            'purchase_item_id' => $item['purchase_item_id'],
            'warehouse_id' => $item['warehouse_id'],
            'product_id' => $item['product_id'],
            'product_variant_id' => $item['product_variant_id'] ?? null,
            'quantity' => $item['return_quantity'],
            'price' => $item['return_price'],
            'sub_total' => $item['return_sub_total'],
        ];
    }
    private function isValidItem($item)
    {
        return isset($item['return_quantity']) && $item['return_quantity'] > 0;
    }

    public function delete($id){
        $data = $this->get($id);
        $data->purchase_return_items()->delete();
        $data->delete();
    }
}
