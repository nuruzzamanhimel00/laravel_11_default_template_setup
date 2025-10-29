<?php

namespace App\Services;

use App\Models\User;
use App\Models\Location;
use App\Models\Purchase;
use App\Models\Warehouse;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class PurchaseService extends BaseService
{
//    protected $fileUploadService;

    public function __construct(Purchase $model)
    {
        parent::__construct($model);
    }
    public function createOrUpdate(Request|array $request, int $id = null): Purchase
    {
        $data           = $request->all();

        $purchaseData = $request->only(['supplier_id','warehouse_id','company','date','address','country','city','zipcode','short_address','notes']);

        try {
            DB::beginTransaction();

            if ($id) {
                // // Update
                $purchase           = $this->get($id)->load(['purchase_items']);
                // dd($purchase);

                $purchaseData['total'] =  collect($data['purchase_items'])->sum('sub_total');

                // Delete existing purchase items
                $purchase->purchase_items()->delete();

                // Usage
                $data['purchase_items'] = $this->decodePurchaseItemsData($data['purchase_items']);

                // Use the relationship to create multiple purchase_items
                $purchase->purchase_items()->createMany($data['purchase_items']);


                $purchase->update($purchaseData);
                DB::commit();

                return $purchase;
            } else {

                $purchaseData = array_merge($purchaseData, [
                    'total' => collect($data['purchase_items'])->sum('sub_total'),
                ]);

                // Create the purchase record
                $purchase = $this->model::create($purchaseData);

                // Usage
                $data['purchase_items'] = $this->decodePurchaseItemsData($data['purchase_items']);

                // Use the relationship to create multiple purchase_items
                $purchase->purchase_items()->createMany($data['purchase_items']);



                DB::commit();
                return $purchase;
            }



        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

    }

    public function decodePurchaseItemsData(array $purchaseItems): array {
        return array_map(function ($item) {
            $item['data'] = json_decode($item['data'], true);
            return $item;
        }, $purchaseItems);
    }
    public function fileUpload($id, $documents){
        $purchase = $this->get( $id);

        if(!empty($documents)){

            foreach($documents as $document){
                if(isset($document['purchase_file'])){
                    if(isset($document['old_purchase_file'])){
                        $old_purchase_file = $document['old_purchase_file'];
                        $this->fileUploadService->delete($old_purchase_file);
                        if($purchase->document != null){
                            $purchase->document()->delete();
                        }
                    }

                    $file = $document['purchase_file'];
                    $filePath = $file->store('documents','public');
                    $extension = $file->getClientOriginalExtension();

                    $purchase->document()->create([
                        'collection_name' => 'purchase_file',
                        'name' => $filePath,
                        'extension' => $extension
                    ]);
                }

            }
        }

        return $purchase;
    }


    public function delete($id){
        $purchase = $this->get($id);

        try {
            DB::beginTransaction();
                $purchase->purchase_items()->delete();

                $purchase->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            //throw $th;
        }

    }

    public  function getActiveAllData()
    {
        $suppliers = User::query()
            ->where('status', User::STATUS_ACTIVE)
            ->where('type', User::TYPE_SUPPLIER)
            ->get();

        $warehouses = Warehouse::query()
            ->where('status', STATUS_ACTIVE)
            ->get();

        return [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
        ];
    }

    public function simpProductPurchaseItemMake($product, $location_id = null){
        $searchItems = [];
        // Check if the product has variants
        if ($product->is_variant && $product->variants->isNotEmpty()) {
            $searchItems = array_merge($searchItems, $product->variants->map(function ($variant) use ($product, $location_id) {
                $avg_purchase_price =  ProductStock::where('product_id', $product->id)
                ->where('variant_id', $variant->id)
                ->where('warehouse_id', $location_id)
                ->value('avg_purchase_price');
                return [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'product_name' => '(SKU: ' . $product->sku . ') ' . $product->name . ' (Size:' . $variant->name . ')',
                    // 'product_name' => '(SKU: ' . $product->sku . ') ' . $product->name . ' (Size:' . $variant->product_attribute_values->pluck('value')->implode(', ') . ')',
                    'product_sku' => $product->sku,
                    'quantity' => 1,
                    'price' => $avg_purchase_price ?? 0,
                    'notes' => '',
                    'sub_total' =>$avg_purchase_price ?? 0,
                    'barcode' => $variant->barcode,
                    // 'size'  => $variant->product_attribute_values->pluck('value')->implode(', '),
                    'size'  => $variant->name,
                    'condition_name' => $variant->condition->name ?? '',
                    'name' =>    $variant->product->name,
                    'avg_price' => $avg_purchase_price ?? 0
                ];
            })->toArray());
        } else {
            // For non-variant products
            $searchItems[] = [
                'product_id' => $product->id,
                'product_variant_id' => null,
                'product_name' => '(SKU: ' . $product->sku . ') ' . $product->name,
                'product_sku' => $product->sku,
                'quantity' => 1,
                'price' => $product->product_stock->avg_purchase_price ?? 0,
                'notes' => '',
                'sub_total' => $product->product_stock->avg_purchase_price ?? 0,
                'barcode' => $product->barcode,
                'size'  => null,
                'condition_name' => $product->condition->name ?? '',
                'name' =>    $product->name,
                'avg_price' => $product->product_stock->avg_purchase_price ?? 0
            ];
        }
        return $searchItems;
    }

    public function getPurchaseProductItems($purchase)
    {
        $productItems = [];

        foreach ($purchase->purchase_items as $purItem) {
            $product = $purItem->product;
            $productItems[] = $this->formatProductItem($purItem, $product);
            // if ($product->is_variant) {
            //     // Fetch product variant with attributes
            //     $productVariant = ProductVariant::with(['product_attribute_values'])
            //     ->leftJoin('product_stocks', function ($join) {
            //         $join->on('product_stocks.product_id', '=', 'product_variants.product_id')
            //              ->on('product_stocks.variant_id', '=', 'product_variants.id');
            //     })
            //         ->find($purItem->product_variant_id);

            //     if ($productVariant) {
            //         $productItems[] = $this->formatProductItem($purItem, $product, $productVariant);
            //     }
            // } else {
            //     // Handle non-variant products
            //     $productItems[] = $this->formatProductItem($purItem, $product);
            // }
        }

        return $productItems;
    }

    public function formatProductItem($purItem, $product, $productVariant = null)
    {
        return [
            'product_id' => $purItem->product_id,
            'product_variant_id' => $productVariant ? $purItem->product_variant_id : null,
            'product_name' => $this->generateProductName($product, $productVariant),
            'product_sku' => $product->sku,
            'quantity' => $purItem->quantity,
            'price' => (int)$purItem->price,
            'sale_price' => (int)$purItem->sale_price,
            'restaurant_sale_price' => (int)$purItem->restaurant_sale_price,
            'notes' => $purItem->notes,
            'sub_total' => (int)$purItem->sub_total,
            'barcode' => $productVariant ? $productVariant->barcode : $product->barcode,
            'size'  => $productVariant->sku ?? null,
            // 'size'  => $productVariant->product_attribute_values->pluck('value')->implode(', ') ?? null,
            'condition_name' => null,
            'name' =>    $product->name,
            'avg_price' => (int)$purItem->price,
        ];
    }

    private function generateProductName($product, $productVariant = null)
    {
        $baseName = '(SKU: ' . $product->sku . ') ' . $product->name;

        if ($productVariant) {
            // $variantAttributes = $productVariant->product_attribute_values->pluck('value')->implode(', ');
            $variantAttributes =  ' (Size:' . $productVariant->sku . ')';
            // $variantAttributes =  ' (Size:' . $productVariant->product_attribute_values->pluck('value')->implode(', ') . ')';
            return $baseName . $variantAttributes ;
        }

        return $baseName;
    }


}
