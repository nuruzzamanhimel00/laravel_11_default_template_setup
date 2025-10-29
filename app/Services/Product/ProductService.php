<?php
namespace App\Services\Product;

use ZipArchive;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService extends BaseService
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function getAllProducts($status=null, $paginate=20)
    {
        $products = Product::getFilteredData(20);
        return $products;
    }

    public function getProductById($id)
    {
        try {
            return $this->model::with(['category:id,name', 'brand:id,name', 'productMeta', 'defaultWarehouseStock'])->findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Product retrieval failed: ' . $e->getMessage());
            throw new \Exception("Product not found.", 404);
        }
    }

    public function createProduct($request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            // Handle image upload for new product
            if($request->hasFile('image')){
                $data['image'] = $this->fileUploadService->uploadFile(
                    $request,
                    'image',
                    $this->model::FILE_STORE_PATH
                );
            }
            if($request->hasFile('details_image')){
                $data['details_image'] = $this->fileUploadService->uploadFile(
                    $request,
                    'details_image',
                    $this->model::FILE_STORE_PATH
                );
            }
            // Process variant status and barcode
            $data['is_variant'] = isset($data['is_variant']) ? true : false;
            if (!$data['is_variant']) {
                $this->processNonVariantProduct($data);
            } else {
                $data['barcode'] = null;
                $data['barcode_image'] = null;
            }

            $data['status'] = isset($request->status) && $request->status == STATUS_ACTIVE ? STATUS_ACTIVE : STATUS_INACTIVE;
            // $data['status'] = STATUS_ACTIVE;

            if(isset($request->taxes) && is_array($data['taxes'])){
                $data['taxes'] = $data['taxes'];
            }

            $product= $this->model::updateOrCreate(['name' => $data['name']], $data);

            // Create product meta
            $this->updateProductMeta($product, $data);

            // Create variants if enabled
            if ($data['is_variant'] && $request->has('variants')) {
                $this->processProductVariants($product, $data['variants']);
            }
            $this->productTagsCreate($product, $request->product_tags);

            DB::commit();
            return $product;
        } catch (\Exception $e) {
            Log::error('Product creation failed: ' . $e->getMessage());
            DB::rollBack();
            throw new \Exception("Failed to create product.", 500);
        }
    }

    public function productTagsCreate($product, $product_tags)
    {
        // Delete existing tags
        $product->product_tags()->delete();

        // Create new tags if any
        if (!empty($product_tags)) {
            $tags = array_map(fn($tag) => ['name' => $tag], $product_tags);
            $product->product_tags()->createMany($tags);
        }
    }


    public function updateProduct($id, $request)
    {
        DB::beginTransaction();
        $data = $request->validated();
        try {
            $product = $this->model::findOrFail($id);
            if($request->hasFile('image')){
                $image = $this->fileUploadService->uploadFile($request,'image',$this->model::FILE_STORE_PATH, $product->image);
                $data['image'] = $image;
            }else{
                $data['image'] = $product->image;
            }
            if($request->hasFile('details_image')){
                $image = $this->fileUploadService->uploadFile($request,'details_image',$this->model::FILE_STORE_PATH, $product->details_image);
                $data['details_image'] = $image;
            }else{
                $data['details_image'] = $product->details_image;
            }

            // Process variant status and barcode
            $data['is_variant'] = isset($data['is_variant']) ? true : false;
            if (!$data['is_variant']) {
                $this->processNonVariantProduct($data, $product);
            } else {
                $data['barcode'] = null;
                $data['barcode_image'] = null;
            }

            // $data['status'] = isset($request->status) ? STATUS_ACTIVE : STATUS_INACTIVE;
            $data['status'] = isset($data['status'] ) && $data['status'] == STATUS_ACTIVE ? STATUS_ACTIVE : STATUS_INACTIVE;
            $data['is_split_sale'] = isset($data['is_split_sale'] ) ? true : false;

            $product->update($data);
            $product->refresh();
            // Create product meta
            $this->updateProductMeta($product, $data);

            $this->productTagsCreate($product, $request->product_tags);

            // Create variants if enabled
            if ($data['is_variant'] && $request->has('variants')) {
                $this->processProductVariants($product, $data['variants']);
            }

            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product update failed: ' . $e->getMessage());
            throw new \Exception("Failed to update product.", 500);
        }
    }

    public function deleteProduct($id)
    {
        try {
            $this->model::findOrFail($id)->delete();
        } catch (\Exception $e) {
            Log::error('Product deletion failed: ' . $e->getMessage());
            throw new \Exception("Failed to delete product.", 500);
        }
    }

    // update or create product meta
    public function updateProductMeta($product, $data)
    {
        $product->productMeta()->updateOrCreate([
            'product_id' => $product->id,
        ],[
            'product_id'  => $product->id,
            'model'       => isset($data['model']) ? $data['model'] : '',
            'gender'      => isset($data['gender']) ? $data['gender'] : '',
            'unit_value'  => isset($data['unit_value']) ? $data['unit_value'] : '',
            'notes'       => isset($data['notes']) ? $data['notes'] : '',
            'description' => isset($data['description']) ? $data['description'] : '',
        ]);
    }
    /**
     * Process non-variant product data
     *
     * @param array $data
     * @param Product|null $product
     */
    private function processNonVariantProduct(array &$data, ?Product $product = null): void
    {
        $data['barcode'] = $data['barcode'] ?? \Carbon\Carbon::now()->timestamp;
        if (isset($data['barcode_image']) && $data['barcode_image'] !== null) {
            $data['barcode_image'] = $this->processBarcodeImage(
                $data['sku']?? rand(1000,9999),
                $data['barcode'],
                $data['barcode_image'],
                $product?->barcode_image
            );
        }
    }

    private function processProductVariants(Product $product, array $variants): void
    {
        if(empty($variants)){
            return;
        }

        $existing_variant_ids = $product->variants()->pluck('id')->toArray();
        $updated_variant_ids = [];

        foreach ($variants as $index => $variant_data) {
            $variant_id = $variant_data['id'] ?? null;
            if ($variant_id && in_array($variant_id, $existing_variant_ids)) {
                $updated_variant_ids[] = $this->updateVariant($product, $variant_data, $variant_id);
            }else{
                $this->createVariant($product, $variant_data);
            }
        }

        // Remove deleted variants
        $deleted_variant_ids = array_diff($existing_variant_ids, $updated_variant_ids);
        if(!empty($deleted_variant_ids)){
            $product->variants()->whereIn('id', $deleted_variant_ids)->delete();
        }
    }

    private function createVariant(Product $product, array $variant_data): void
    {
        $variant_data['product_id'] = $product->id;
        $variant_data['barcode'] = $variant_data['barcode'] ?? \Carbon\Carbon::now()->timestamp;
        if (isset($variant_data['barcode_image']) && $variant_data['barcode_image'] !== null) {
            $variant_data['barcode_image'] = $this->processBarcodeImage(
                $product->sku ?? 'sku',
                $variant_data['barcode'],
                $variant_data['barcode_image']
            );
        }

        $combination = $this->generateCombinationString($variant_data['attribute_values']);
        if(!empty($variant_data['sku'])){
            $combination = $variant_data['sku'];
        }
        $name = $this->generateVariantName($variant_data['attribute_values']);
        // $sku = $combination;

        // dd($combination, $name, $sku, $variant_data);
        $variant = $product->variants()->create([
            'barcode' => $variant_data['barcode'],
            'barcode_image' => $variant_data['barcode_image'],
            'sale_price' => $variant_data['sale_price'] ?? 0,
            'purchase_price' => $variant_data['purchase_price'] ?? 0,
            'stock_qty' => $variant_data['stock_qty'] ?? $product->stock_qty,
            'low_stock_alert' => $variant_data['low_stock_alert'] ?? $product->low_stock_alert,
            'color' => $variant_data['color'] ?? '',
            // 'status' => $variant_data['status'] ?? ProductVariant::STATUS_ACTIVE,
            'is_default' => isset($variant_data['is_default']),
            'product_condition_id' => $variant_data['product_condition_id'] ?? null,
            'name' => $name ?? '',
            'sku' => $combination ?? '',
        ]);

        $this->processVariantAttributes($variant, $variant_data);
    }

    /**
     * Update existing variant
     *
     * @param Product $product
     * @param array $variant_data
     * @param int $variant_id
     * @return int
     */
    // private function updateVariant(Product $product, array $variant_data, int $variant_id): int
    // {
    //     $variant = ProductVariant::find($variant_id);
    //     if(!$variant){
    //         return $variant_id;
    //     }
    //     // check barcode image as file
    //     // dd($this->validateBase64Image($variant_data['barcode_image']), $variant_data['barcode_image']);
    //     if (isset($variant_data['barcode_image']) && $variant_data['barcode_image'] !== null && $this->validateBase64Image($variant_data['barcode_image'])) {
    //         $variant_data['barcode_image'] = $this->processBarcodeImage(
    //             'barcode-sku',
    //             $variant_data['barcode'],
    //             $variant_data['barcode_image'],
    //             $variant->barcode_image
    //         );
    //     }else{
    //         $variant_data['barcode_image'] = $variant->barcode_image;
    //     }
    //     $combination = $this->generateCombinationString($variant_data['attribute_values']);
    //     // $name = $variant_data['name'] ?? $product->name . ' - ' . $combination;
    //     // $sku = $combination;
    //     $name = $this->generateVariantName($variant_data['attribute_values']);

    //     $variant->update([
    //         'barcode' => $variant_data['barcode'] ?? \Carbon\Carbon::now()->timestamp,
    //         'barcode_image' => $variant_data['barcode_image'],
    //         'name' => $name ?? '',
    //         'sku' => $combination ?? '',
    //         'sale_price' => $variant_data['sale_price'] ?? $variant->sale_price,
    //         'purchase_price' => $variant_data['purchase_price'] ?? $variant->purchase_price,
    //         'stock_qty' => $variant_data['stock_qty'] ?? $product->stock_qty,
    //         'low_stock_alert' => $variant_data['low_stock_alert'] ?? $product->low_stock_alert,
    //         'color' => $variant_data['color'] ?? '',
    //         // 'status' => $variant_data['status'] ?? ProductVariant::STATUS_ACTIVE,
    //         'is_default' => isset($variant_data['is_default']),
    //         'product_condition_id' => $variant_data['product_condition_id'] ?? null
    //     ]);

    //     $this->processVariantAttributes($variant, $variant_data);

    //     return $variant_id;
    // }

    /**
     * Process variant attributes
     *
     * @param ProductVariant $variant
     * @param array $variant_data
     */
    // private function processVariantAttributes(ProductVariant $variant, array $variant_data): void
    // {
    //     if (!isset($variant_data['attribute_values'])) {
    //         return;
    //     }

    //     foreach ($variant_data['attribute_values'] as $attribute_id => $value) {
    //         $prod_attr_value = ProductAttributeValue::where('product_attribute_id', $attribute_id)->where('id', $value)->orWhere('value', $value)->first();
    //         if ($attribute_id && $prod_attr_value) {
    //             // ProductVariantAttribute::updateOrCreate(
    //             //     [
    //             //         'product_attribute_id' => $attribute_id,
    //             //         'product_variant_id' => $variant->id
    //             //     ],
    //             //     [
    //             //         'product_attribute_value_id' => $prod_attr_value?->id,
    //             //         'product_attribute_value' => $prod_attr_value?->value
    //             //     ]
    //             // );
    //         }
    //     }
    // }

    /**
     * Process and store barcode image
     *
     * @param string $sku
     * @param string $barcode
     * @param string $base64image
     * @param string|null $old_name
     * @return string|null
     */
    public function processBarcodeImage(string $sku = '', string $barcode, string $base64image, ?string $old_name = null): ?string
    {
        try {
            // Delete old image if exists
            if ($old_name && Storage::disk(config('filesystems.default'))->exists($old_name)) {
                Storage::disk(config('filesystems.default'))->delete($old_name);
            }

            $name = ($sku ? "{$sku}_" : '') . $barcode . '.png';
            $img = substr($base64image, strpos($base64image, ",") + 1);
            $file_path = $this->model::FILE_BARCODE_PATH . '/' . $name;

            Storage::disk(config('filesystems.default'))->put($file_path, base64_decode($img));
            return $file_path;
        } catch (\Exception $e) {
            logger($e->getMessage());
            return null;
        }
    }

    // generate combination string of attributes
    public function generateCombinationString($attributes){
        $combination = '';
        foreach($attributes as $attribute_id => $attribute_value_id){
            // cached attribute_values data
            $attribute_value = Cache::remember('attribute_value_' . $attribute_value_id, 60, function () use ($attribute_value_id)  {
                return ProductAttributeValue::with('attribute')->find($attribute_value_id);
            });
            // $attribute_value = ProductAttributeValue::with('attribute')->find($attribute_value_id);
            // get first 3 letter of attribute name
            $attribute_name = substr($attribute_value?->attribute?->name, 0, 3);
            // remove all spaces
            $attribute_name = str_replace(' ', '', $attribute_name);
            $combination_string = $attribute_name . '-' . $attribute_value?->value . '-';
            $combination .= $combination_string;
            // $combination .= $attribute_value?->value . '-';
        }
        // also remove all extra spaces
        $combination = str_replace(' ', '', rtrim($combination, '-'));
        return Str::upper($combination);
    }
    public function generateVariantName($attributes){
        $combination = '';
        foreach($attributes as $attribute_id => $attribute_value_id){
            // $attribute_value = ProductAttributeValue::with('attribute')->find($attribute_value_id);
            $attribute_value = Cache::remember('attribute_value_' . $attribute_value_id, 60, function () use ($attribute_value_id)  {
                return ProductAttributeValue::with('attribute')->find($attribute_value_id);
            });

            // $combination .= $attribute_value?->attribute?->name . '-' . $attribute_value?->value . '-';
            $combination .= strtoupper($attribute_value?->value . '-');
        }
        return rtrim($combination, '-');
    }

    public function barcodeDownloadZip($zipPath,$files){
        // Create a new ZIP archive
        // dd($zipPath,$files);
        $zip = new ZipArchive;
        $allProductBarcode = Storage::allFiles('products/barcode');
        // dd($allProductBarcode);

        if(count($files) > 0){
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                // Add files to the ZIP archive
                foreach ($files as $file) {
                    $publicPathFile =  public_path('storage/'.$file);
                    // dd('publicPathFile',$publicPathFile);
                    if (file_exists($publicPathFile) && in_array($file, $allProductBarcode)) {

                        $zip->addFile($publicPathFile, basename($publicPathFile));
                    }
                }
                $zip->close();
            } else {
                return response()->json(['error' => 'Unable to create ZIP file'], 500);
            }

            // Return the ZIP file as a response
            return response()->download($zipPath)->deleteFileAfterSend(true);
        }else{
            sendFlash('File not found.', 'error');
            return false;
        }

    }

    public function barcodeDownload($barcode_image){
        $disk = Storage::disk(config('filesystems.default')); // Specify your disk
        $filePath = $barcode_image;

        if (!$disk->exists($filePath)) {
            sendFlash('File not found.', 'error');
            return back();

        }
        return $disk->download($filePath);
    }

    // public function getAll($category_id = null, $brand_id = null)
    public function getAll(
        array $selectedFields = ['id', 'product_condition_id', 'category_id', 'brand_id', 'product_unit_id', 'name', 'image', 'sku', 'barcode', 'barcode_image', 'purchase_price', 'sale_price', 'restaurant_sale_price', 'status', 'total_stock_quantity', 'low_stock_alert', 'available_for', 'is_split_sale', 'taxes', 'meta', 'created_at'],
        array $withRelations = ['warehouse_stock', 'category', 'brand', 'productUnit', 'productMeta', 'latest_promotion_item','product_tags']
    )
    {
        $request = request();

        $per_page = $request->get('par_page', 20);


        $brand_id =  $request->get('brand_id') ?? null;
        $category_id =  $request->get('category_id') ?? null;

        // $stock_type = $request->get('stock_type','in_stock'); // in_stock/out_of_stock
        $sort_price = $request->get('sort_price'); // 'asc' or 'desc'

        $availableFor = auth('api')->check() ? auth('api')->user()->type: null; // 'Restaurant' or 'Customer'
        $search = $request->get('search');



        $now = Carbon::now();

        $category_ids = [];
        if ($category_id) {
            $category = Category::with(['childs:id,parent_id'])->find($category_id);
            $category_ids = [(int)$category_id];

            if ($category && $category->childs->isNotEmpty()) {
                $category_ids = array_merge($category_ids, $category->childs->pluck('id')->toArray());
            }
        }


        $relationships = [];

        if (in_array('warehouse_stock', $withRelations)) {
            $relationships['warehouse_stock'] = function ($q) {
                $q->select('id', 'product_id', 'stock_quantity');
            };
        }

        if (in_array('category', $withRelations)) {
            $relationships['category'] = function ($q) {
                $q->select('id', 'name', 'slug', 'image', 'description', 'status');
            };
        }

        if (in_array('brand', $withRelations)) {
            $relationships['brand'] = function ($q) {
                $q->select('id', 'name', 'image', 'status');
            };
        }

        if (in_array('productUnit', $withRelations)) {
            $relationships['productUnit'] = function ($q) {
                $q->select('id', 'name', 'symbol', 'type');
            };
        }

        if (in_array('productMeta', $withRelations)) {
            $relationships['productMeta'] = function ($q) {
                $q->select('id', 'product_id', 'unit_value', 'notes', 'description');
            };
        }

        if (in_array('latest_promotion_item', $withRelations)) {
            $relationships['latest_promotion_item'] = function ($q) use ($availableFor, $now, $selectedFields) {
                $q->with(['promotion'=>function($q){
                    $q->select('id','title','image','message','offer_type','offer_value');
                }, 'category'])
                    ->whereHas('promotion', function ($query) use ($availableFor, $now) {
                        $query->where('target_type', $availableFor)
                              ->whereDate('start_date', '<=', $now)
                              ->whereDate('end_date', '>=', $now);
                    });
            };
        }
        if (in_array('product_tags', $withRelations)) {
            $relationships['product_tags'] = function ($q) {
                $q->select('id', 'product_id','name');
            };
        }


        $query = $this->model
            ->active()
            ->select($selectedFields)
            ->with($relationships)
            ->when($brand_id, fn($q) => $q->where('brand_id', $brand_id))
            ->when($category_id && count($category_ids) > 0, fn($q) => $q->whereIn('category_id', $category_ids))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('brand', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('product_tags', fn($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->availableFor()
            ->hasActiveCategory()
            ->hasActiveBrand();
            // ->stockAvailable();


        // Apply price sorting
        if(!empty($sort_price) && !empty($availableFor)  && in_array($sort_price, ['asc', 'desc'])){
            if ($availableFor === User::TYPE_RESTAURANT) {
                $query->orderBy('restaurant_sale_price', $sort_price);
            } else {
                $query->orderBy('sale_price', $sort_price);
            }
        }
        elseif(!empty($sort_price) && empty($availableFor) && in_array($sort_price, ['asc', 'desc'])){
            $query->orderBy('sale_price', $sort_price);
        }else{
            $query->latest(); // Default sorting
        }


        return $query->paginate($per_page);
    }



    public function getDetails($id){

        // $available_for = request()->get('available_for'); // 'Restaurant' or 'Customer'
        $available_for = auth('api')->check() ? auth('api')->user()->type : null;
        $now = Carbon::now();

        return $this->model->active()
            ->select([
                'id',
                'category_id',
                'brand_id',
                'product_unit_id',
                'name',
                'image',
                'sku',
                'barcode',
                'barcode_image',
                'purchase_price',
                'sale_price',
                'restaurant_sale_price',
                'status',
                'total_stock_quantity',
                'low_stock_alert',
                'is_variant',
                'available_for',
                'is_split_sale',
                'taxes',
                'meta',
                'created_at',
                'details_image'
            ])
            ->with([
                'warehouse_stock:id,product_id,stock_quantity',
                'category:id,name,slug,image,description,status',
                'brand:id,name,image,status',
                'productUnit:id,name,symbol,type',
                'productMeta:id,product_id,unit_value,notes,description',
                'latest_promotion_item' => function ($query) use ($available_for , $now) {
                    return $query->with(['promotion'=>function($q){
                            $q->select('id','title','image','message','offer_type','offer_value');
                        }])
                    ->whereHas('promotion', function ($query) use ($available_for, $now) {
                        $query->where('target_type', $available_for)
                        ->whereDate('start_date', '<=', $now)
                        ->whereDate('end_date', '>=', $now);
                    });
                }
            ])

            ->availableFor()
            ->hasActiveCategory()
            ->hasActiveBrand()
            // ->stockAvailable()

            ->find($id);
    }
    // public function relatedItems($id){

    //     $request = request();
    //      // Fetch the current product
    //     $product = $this->model->findOrFail($id);

    //     $per_page = $request->get('par_page', 20);
    //     // $brand_id = $product->brand_id;
    //     // $category_id =  $product->brand_id;
    //     // $new_arrival_days = $request->get('new_arrival_days');
    //     $stock_type = $request->get('stock_type') ?? 'in_stock';
    //     // $sort_price = $request->get('sort_price'); // 'asc' or 'desc'
    //     $available_for = request()->get('available_for'); // 'Restaurant' or 'Customer'
    //     $now = Carbon::now();

    //     $query = $this->model
    //         ->active()
    //         ->select([
    //             'id', 'category_id', 'brand_id', 'product_unit_id', 'name', 'image', 'sku',
    //             'barcode', 'barcode_image', 'purchase_price', 'sale_price', 'restaurant_sale_price',
    //             'status', 'total_stock_quantity', 'low_stock_alert', 'is_variant', 'available_for',
    //             'is_split_sale', 'taxes', 'meta', 'created_at'
    //         ])
    //         ->with([
    //             'warehouse_stock:id,product_id,stock_quantity',
    //             'category:id,name,slug,image,description,status',
    //             'brand:id,name,image,status',
    //             'productUnit:id,name,symbol,type',
    //             'productMeta:id,product_id,unit_value,notes,description',
    //             'latest_promotion_item' => function ($query) use ($available_for , $now) {
    //                 return $query->with(['promotion','category','product'])
    //                 ->whereHas('promotion', function ($query) use ($available_for, $now) {
    //                     $query->where('target_type', $available_for)
    //                     ->whereDate('start_date', '<=', $now)
    //                     ->whereDate('end_date', '>=', $now);
    //                 });
    //             }
    //         ])
    //         ->where('id', '!=', $product->id) // Exclude the current product
    //         ->where(function ($q) use ($product) {
    //             $q->where('category_id', $product->category_id)
    //               ->orWhere('brand_id', $product->brand_id);
    //         })
    //         ->availableFor()
    //         ->hasActiveCategory()
    //         ->hasActiveBrand()
    //         ->stockAvailable($stock_type);

    //     // // Filter by new arrivals if applicable
    //     // if (!empty($new_arrival_days)) {
    //     //     $query->newArrival($new_arrival_days);
    //     // }

    //     // Apply price sorting
    //     $query = $query->latest(); // Default sorting

    //     return $query->paginate($per_page);
    // }

    public function relatedItems($id,     array $selectedFields = ['id', 'product_condition_id', 'category_id', 'brand_id', 'product_unit_id', 'name', 'image', 'sku', 'barcode', 'barcode_image', 'purchase_price', 'sale_price', 'restaurant_sale_price', 'status', 'total_stock_quantity', 'low_stock_alert', 'available_for', 'is_split_sale', 'taxes', 'meta', 'created_at'],
    array $withRelations = ['warehouse_stock', 'category', 'brand', 'productUnit', 'productMeta', 'latest_promotion_item','details_image']){

        $request = request();
         // Fetch the current product
        $product = $this->model->findOrFail($id);

        $per_page = $request->get('par_page', 20);
        $availableFor = auth('api')->check() ? auth('api')->user()->type: null;
        $now = Carbon::now();

        // Prepare relationships with constraints where needed
        $relationships = [];

        if (in_array('warehouse_stock', $withRelations)) {
            $relationships['warehouse_stock'] = function ($q) {
                $q->select('id', 'product_id', 'stock_quantity');
            };
        }

        if (in_array('category', $withRelations)) {
            $relationships['category'] = function ($q) {
                $q->select('id', 'name', 'slug', 'image', 'description', 'status');
            };
        }

        if (in_array('brand', $withRelations)) {
            $relationships['brand'] = function ($q) {
                $q->select('id', 'name', 'image', 'status');
            };
        }

        if (in_array('productUnit', $withRelations)) {
            $relationships['productUnit'] = function ($q) {
                $q->select('id', 'name', 'symbol', 'type');
            };
        }

        if (in_array('productMeta', $withRelations)) {
            $relationships['productMeta'] = function ($q) {
                $q->select('id', 'product_id', 'unit_value', 'notes', 'description');
            };
        }

        if (in_array('latest_promotion_item', $withRelations)) {
            $relationships['latest_promotion_item'] = function ($q) use ($availableFor, $now, $selectedFields) {
                $q->with(['promotion'=>function($q){
                    $q->select('id','title','image','message','offer_type','offer_value');
                }, 'category', 'product'=>function ($q) use($selectedFields) {
                    $q->select($selectedFields);
                }])
                    ->whereHas('promotion', function ($query) use ($availableFor, $now) {
                        $query->where('target_type', $availableFor)
                              ->whereDate('start_date', '<=', $now)
                              ->whereDate('end_date', '>=', $now);
                    });
            };
        }


        $query = $this->model
            ->active()
            ->select($selectedFields)
            ->with($relationships)
            ->where('id', '!=', $product->id) // Exclude the current product
            ->where(function ($q) use ($product) {
                $q->where('category_id', $product->category_id)
                  ->orWhere('brand_id', $product->brand_id);
            })
            ->availableFor()
            ->hasActiveCategory()
            ->hasActiveBrand();
            // ->stockAvailable($stock_type);


        // Apply price sorting
        $query = $query->orderBy('id','desc'); // Default sorting

        return $query->paginate($per_page);
    }
    // public function oldGetBestSellingProducts(){

    //     $per_page = request()->get('par_page', 20);
    //     $best_sell_days = request()->get('best_sell_days', 30);
    //     $product_collects = collect([]);
    //     $available_for = request()->get('available_for'); // 'Restaurant' or 'Customer'
    //     $now = Carbon::now();

    //     $query = OrderItem::query()
    //     ->whereHas('order', function ($query) {
    //         $query->where('order_status', Order::STATUS_ORDER_PACKAGED)
    //             ->where('delivery_status', Order::STATUS_DELIVERY_COMPLETE);
    //     })
    //     ->select('product_id', DB::raw('SUM(quantity) as total'))

        // ->with([
        //     'product',
        //     'product.warehouse_stock:id,product_id,stock_quantity',
        //     'product.category:id,name,slug,image,description,status',
        //     'product.brand:id,name,image,status',
        //     'product.productUnit:id,name,symbol,type',
        //     'product.productMeta:id,product_id,unit_value,notes,description',
        //     'product.latest_promotion_item' => function ($query) use ($available_for , $now) {
        //             return $query->with(['promotion','category','product'])
        //             ->whereHas('promotion', function ($query) use ($available_for, $now) {
        //                 $query->where('target_type', $available_for)
        //                 ->whereDate('start_date', '<=', $now)
        //                 ->whereDate('end_date', '>=', $now);
        //             });
        //         }
        // ])
    //     ->whereHas('product', function ($query) {
    //         $query->active()->availableFor();
    //     })
    //     ->whereHas('product.category', function ($query) {
    //         $query->active();
    //     })
    //     ->whereHas('product.brand', function ($query) {
    //         $query->active();
    //     })

    //     ->groupBy('product_id')
    //     ->orderByDesc('total');

    //     if (!empty($best_sell_days)) {
    //         $query->whereHas('order', function ($q) use($best_sell_days) {
    //             $q->whereDate('date', '>=', now()->subDays($best_sell_days));
    //         });
    //     }
    //     $query = $query->paginate($per_page);
    //     // dd($query);
    //     if($query->count() > 0){
    //         foreach($query as $item){
    //             $product_collects->push($item->product);
    //         }
    //     }
        // $product_collects = $product_collects->paginate($per_page);

        // return $product_collects;
    // }

    // public function old2getBestSellingProducts(
    //     array $selectedFields = ['id', 'product_condition_id', 'category_id', 'brand_id', 'product_unit_id', 'name', 'image', 'sku', 'barcode', 'barcode_image', 'purchase_price', 'sale_price', 'restaurant_sale_price', 'status', 'total_stock_quantity', 'low_stock_alert', 'available_for', 'is_split_sale', 'taxes', 'meta', 'created_at'],
    //     array $withRelations = ['warehouse_stock', 'category', 'brand', 'productUnit', 'productMeta', 'latest_promotion_item']
    // ) {
    //     $request = request();
    //     $perPage = request()->get('par_page', 20);
    //     $bestSellDays = request()->get('best_sell_days', 60);
    //     // $availableFor = request()->get('available_for'); // 'Restaurant' or 'Customer'
    //     $availableFor = auth('api')->check() ? auth('api')->user()->type: null;
    //     $now = now();
    //     $productCollects = collect();
    //     $search = $request->get('search');



    //     // Prepare relationships with constraints where needed
    //     $relationships = [];

    //     if (in_array('warehouse_stock', $withRelations)) {
    //         $relationships['warehouse_stock'] = function ($q) {
    //             $q->select('id', 'product_id', 'stock_quantity');
    //         };
    //     }

    //     if (in_array('category', $withRelations)) {
    //         $relationships['category'] = function ($q) {
    //             $q->select('id', 'name', 'slug', 'image', 'description', 'status');
    //         };
    //     }

    //     if (in_array('brand', $withRelations)) {
    //         $relationships['brand'] = function ($q) {
    //             $q->select('id', 'name', 'image', 'status');
    //         };
    //     }

    //     if (in_array('productUnit', $withRelations)) {
    //         $relationships['productUnit'] = function ($q) {
    //             $q->select('id', 'name', 'symbol', 'type');
    //         };
    //     }

    //     if (in_array('productMeta', $withRelations)) {
    //         $relationships['productMeta'] = function ($q) {
    //             $q->select('id', 'product_id', 'unit_value', 'notes', 'description');
    //         };
    //     }

    //     if (in_array('latest_promotion_item', $withRelations)) {
    //         $relationships['latest_promotion_item'] = function ($q) use ($availableFor, $now, $selectedFields) {
    //             $q->with(['promotion'=>function($q){
    //                 $q->select('id','title','image','message','offer_type','offer_value');
    //             }, 'category', 'product'=>function ($q) use($selectedFields) {
    //                 $q->select($selectedFields);
    //             }])
    //                 ->whereHas('promotion', function ($query) use ($availableFor, $now) {
    //                     $query->where('target_type', $availableFor)
    //                           ->whereDate('start_date', '<=', $now)
    //                           ->whereDate('end_date', '>=', $now);
    //                 });
    //         };
    //     }

    //     $query = OrderItem::query()
    //         ->whereHas('order', function ($q) {
    //             $q->where('order_status', Order::STATUS_ORDER_PACKAGED)
    //               ->where('delivery_status', Order::STATUS_DELIVERY_COMPLETE);
    //         })
    //         ->select('product_id', DB::raw('SUM(quantity) as total'))
    //         ->with([
    //             'product' => function ($q) use ($selectedFields, $relationships, $search) {
    //                 $q->select($selectedFields)->with($relationships,$search)
    //                 ->when($search, function ($q) use ($search) {
    //                     $q->where(function ($q) use ($search) {
    //                         $q->where('name', 'like', "%{$search}%")
    //                         ->orWhere('sku', 'like', "%{$search}%")
    //                         ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%{$search}%"))
    //                         ->orWhereHas('brand', fn($q) => $q->where('name', 'like', "%{$search}%"))
    //                         ->orWhereHas('product_tags', fn($q) => $q->where('name', 'like', "%{$search}%"));
    //                     });
    //                 })
    //                 ->orderBy('id', 'desc');
    //             }
    //         ])
    //         ->whereHas('product', function ($q) {
    //             $q->active()->availableFor();
    //             // ->stockAvailable();
    //         })
    //         ->whereHas('product.category', function ($q) {
    //             $q->active();
    //         })
    //         ->whereHas('product.brand', function ($q) {
    //             $q->active();
    //         })
    //         ->groupBy('product_id')
    //         ->orderByDesc('total');

    //     if (!empty($bestSellDays)) {
    //         $query->whereHas('order', function ($q) use ($bestSellDays) {
    //             $q->whereDate('date', '>=', now()->subDays($bestSellDays));
    //         });
    //     }

    //     $results = $query->paginate($perPage);

    //     foreach ($results as $item) {
    //         if ($item->product) {
    //             $productCollects->push($item->product);
    //         }
    //     }

    //     $product_collects = $productCollects->paginate($perPage);

    //     return $product_collects;
    // }


    public function getBestSellingProducts(
        array $selectedFields = ['id', 'product_condition_id', 'category_id', 'brand_id', 'product_unit_id', 'name', 'image', 'sku', 'barcode', 'barcode_image', 'purchase_price', 'sale_price', 'restaurant_sale_price', 'status', 'total_stock_quantity', 'low_stock_alert', 'available_for', 'is_split_sale', 'taxes', 'meta', 'created_at'],
        array $withRelations = ['warehouse_stock', 'category', 'brand', 'productUnit', 'productMeta', 'latest_promotion_item']
    ) {
        $request = request();
        $perPage = $request->get('par_page', 20);
        $bestSellDays = $request->get('best_sell_days', 60);
        $availableFor = auth('api')->check() ? auth('api')->user()->type : null;
        $now = now();
        $search = $request->get('search');

        // Prepare constrained relationships
        $relationships = [];

        if (in_array('warehouse_stock', $withRelations)) {
            $relationships['warehouse_stock'] = fn($q) => $q->select('id', 'product_id', 'stock_quantity');
        }

        if (in_array('category', $withRelations)) {
            $relationships['category'] = fn($q) => $q->select('id', 'name', 'slug', 'image', 'description', 'status');
        }

        if (in_array('brand', $withRelations)) {
            $relationships['brand'] = fn($q) => $q->select('id', 'name', 'image', 'status');
        }

        if (in_array('productUnit', $withRelations)) {
            $relationships['productUnit'] = fn($q) => $q->select('id', 'name', 'symbol', 'type');
        }

        if (in_array('productMeta', $withRelations)) {
            $relationships['productMeta'] = fn($q) => $q->select('id', 'product_id', 'unit_value', 'notes', 'description');
        }

        if (in_array('latest_promotion_item', $withRelations)) {
            $relationships['latest_promotion_item'] = function ($q) use ($availableFor, $now, $selectedFields) {
                $q->with([
                    'promotion' => fn($q) => $q->select('id', 'title', 'image', 'message', 'offer_type', 'offer_value'),
                    'category',
                    'product' => fn($q) => $q->select($selectedFields),
                ])
                ->whereHas('promotion', function ($query) use ($availableFor, $now) {
                    $query->where('target_type', $availableFor)
                        ->whereDate('start_date', '<=', $now)
                        ->whereDate('end_date', '>=', $now);
                });
            };
        }

        // Main query: top-selling product_ids
        $query = OrderItem::query()
            ->whereHas('order', function ($q) {
                $q->where('order_status', Order::STATUS_ORDER_PACKAGED)
                ->where('delivery_status', Order::STATUS_DELIVERY_COMPLETE);
            })
            ->select('product_id', DB::raw('SUM(quantity) as total'))
            ->with([
                'product' => function ($q) use ($selectedFields, $relationships, $search) {
                    $q->select($selectedFields)
                    ->with($relationships)
                    ->when($search, function ($q) use ($search) {
                        $q->where(function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('sku', 'like', "%{$search}%")
                                ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%{$search}%"))
                                ->orWhereHas('brand', fn($q) => $q->where('name', 'like', "%{$search}%"))
                                ->orWhereHas('product_tags', fn($q) => $q->where('name', 'like', "%{$search}%"));
                        });
                    })
                    ->orderBy('id', 'desc');
                }
            ])
            ->whereHas('product', function ($q) {
                $q->active()->availableFor(); // Optional: ->stockAvailable();
            })
            ->whereHas('product.category', fn($q) => $q->active())
            ->whereHas('product.brand', fn($q) => $q->active())
            ->groupBy('product_id')
            ->orderByDesc('total');

        if (!empty($bestSellDays)) {
            $query->whereHas('order', function ($q) use ($bestSellDays) {
                $q->whereDate('date', '>=', now()->subDays($bestSellDays));
            });
        }

        // Paginate OrderItems
        $results = $query->paginate($perPage);

        // Extract related products
        $products = $results->getCollection()->pluck('product')->filter();

    // Rebuild paginated products list
    $product_collects = new LengthAwarePaginator(
        $products->values(),
        $results->total(),
        $perPage,
        $results->currentPage(),
        ['path' => request()->url(), 'query' => request()->query()]
    );

    return $product_collects;
}




    // public function oldGetNewArrivalProducts()
    // {
    //     $request = request();
    //     $perPage = $request->get('par_page', 20);
    //     $newArrivalDays = $request->get('new_arrival_days', 30);
    //     $stockType = $request->get('stock_type', 'in_stock'); // 'in_stock' or 'out_of_stock'

    //     $available_for = request()->get('available_for'); // 'Restaurant' or 'Customer'
    //     $now = Carbon::now();

    //     $query = $this->model
    //         ->active()
    //         ->select([
    //             'products.*',
    //             DB::raw('(SELECT MAX(received_at) FROM warehouse_stocks WHERE warehouse_stocks.product_id = products.id) as latest_stock_update')
    //         ])
    //         ->with([
    //             'warehouse_stock:id,product_id,stock_quantity',
    //             'productUnit:id,name,symbol,type',
    //             'productMeta:id,product_id,unit_value,notes,description',
    //             'latest_promotion_item' => function ($query) use ($available_for , $now) {
    //                 return $query->with(['promotion','category','product'])
    //                 ->whereHas('promotion', function ($query) use ($available_for, $now) {
    //                     $query->where('target_type', $available_for)
    //                     ->whereDate('start_date', '<=', $now)
    //                     ->whereDate('end_date', '>=', $now);
    //                 });
    //             }
    //         ])
    //         ->availableFor()
    //         ->hasActiveCategory()
    //         ->hasActiveBrand()
    //         ->stockAvailable($stockType);

    //     // Filter by new arrival
    //     if (!empty($newArrivalDays)) {
    //         $query->whereHas('warehouse_stock', function ($q) use ($newArrivalDays) {
    //             $q->where('received_at', '>=', now()->subDays($newArrivalDays));
    //         });
    //     }

    //     // ✅ Correct ordering by latest warehouse_stock update
    //     $query->orderByDesc('latest_stock_update');

    //     return $query->paginate($perPage);
    // }

    public function getNewArrivalProducts(
        array $selectedFields = [
            'id', 'product_condition_id', 'category_id', 'brand_id', 'product_unit_id',
            'name', 'image', 'sku', 'barcode', 'barcode_image',
            'purchase_price', 'sale_price', 'restaurant_sale_price', 'status',
            'total_stock_quantity', 'low_stock_alert', 'available_for',
            'is_split_sale', 'taxes', 'meta', 'created_at'
        ],
        array $withRelations = ['warehouse_stock', 'productUnit', 'productMeta', 'latest_promotion_item']
    ) {
        // Add subquery for ordering by latest stock update
        $selectedFields[] = DB::raw('(SELECT MAX(received_at) FROM warehouse_stocks WHERE warehouse_stocks.product_id = products.id) as latest_stock_update');

        $request = request();
        $perPage = (int) $request->get('par_page', 20);
        $newArrivalDays = (int) $request->get('new_arrival_days', 30);
        // $stockType = $request->get('stock_type', 'in_stock'); // 'in_stock' or 'out_of_stock'

        $availableFor = auth('api')->check() ? auth('api')->user()->type: null;
        $now = now();
        $search = $request->get('search');

        // Dynamically configure relationship loading
        $relationships = [];

        if (in_array('warehouse_stock', $withRelations)) {
            $relationships['warehouse_stock'] = fn($q) => $q->select('id', 'product_id', 'stock_quantity');
        }

        if (in_array('productUnit', $withRelations)) {
            $relationships['productUnit'] = fn($q) => $q->select('id', 'name', 'symbol', 'type');
        }

        if (in_array('productMeta', $withRelations)) {
            $relationships['productMeta'] = fn($q) => $q->select('id', 'product_id', 'unit_value', 'notes', 'description');
        }

        if (in_array('latest_promotion_item', $withRelations)) {
            $relationships['latest_promotion_item'] = function ($q) use ($availableFor, $now, $selectedFields) {
                $q->with(['promotion'=>function($q){
                    $q->select('id','title','image','message','offer_type','offer_value');
                }, 'category', 'product'=> function ($q) use($selectedFields) {
                    $q->select($selectedFields);
                }])
                  ->whereHas('promotion', function ($query) use ($availableFor, $now) {
                      $query->where('target_type', $availableFor)
                            ->whereDate('start_date', '<=', $now)
                            ->whereDate('end_date', '>=', $now);
                  });
            };
        }

        // Build the query
        $query = $this->model
            ->active()
            ->select($selectedFields)
            ->with($relationships)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('brand', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('product_tags', fn($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->availableFor()
            ->hasActiveCategory()
            ->hasActiveBrand();
            // ->stockAvailable($stockType);

        // Filter for new arrivals
        if ($newArrivalDays > 0) {
            $query->whereHas('warehouse_stock', function ($q) use ($newArrivalDays) {
                $q->where('received_at', '>=', now()->subDays($newArrivalDays));
            });
        }

        // Order by latest stock update
        $query->orderByDesc('latest_stock_update');

        return $query->paginate($perPage);
    }

    public function getLowStockProducts(array $selectedFields=[
        'id','name','category_id','brand_id','image','barcode','total_stock_quantity'
    ],
    array $withRelations = ['category']
    ){

        $relationships = [];

        if (in_array('category', $withRelations)) {
            $relationships['category'] = fn($q) => $q->select('id', 'name');
        }

        $products = Product::query()
        ->select($selectedFields)
        ->with($relationships)
        ->where('total_stock_quantity', '<=', LOW_STOCK_ALERT)->get();
        return $products;
    }


}
