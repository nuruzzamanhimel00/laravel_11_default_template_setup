<?php

namespace App\Http\Controllers\Admin\Product;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use App\Services\ReviewService;
use App\Models\ProductAttribute;
use App\DataTables\ProductDataTable;
use App\Http\Controllers\Controller;
use App\Services\Product\ProductService;
use App\DataTables\ProductReviewsDataTable;
use App\Http\Requests\Api\V1\ReviewRequest;
use App\DataTables\LowStockProductDataTable;
use App\Http\Requests\Product\ProductRequest;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class ProductController extends Controller
{
    protected $productService;
    protected $reviewService;

    public function __construct(ProductService $productService, ReviewService $reviewService)
    {
        $this->productService = $productService;
        $this->reviewService = $reviewService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Product'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Product'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit Product'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete Product'), only: ['destroy']),
            new Middleware(PermissionMiddleware::using('Reviews Product'), only: ['productReviews']),
            new Middleware(PermissionMiddleware::using('Reviews Edit Product'), only: ['productReviewsEdit','productReviewsUpdate']),
            new Middleware(PermissionMiddleware::using('Reviews Delete Product'), only: ['productReviewsDelete']),
        ];
    }

    public function index(ProductDataTable $dataTable)
    {
        setPageMeta('Product List');
        setCreateRoute(route('products.create'),'route');
        return $dataTable->render('admin.product.index');
    }

    public function create()
    {
        setPageMeta('Product create');
        setCreateRoute(null);
        $categories = Category::select(['id', 'name','parent_id'])
        ->with(['childs'=>function($query){
            $query->select(['id', 'name','parent_id'])->active()->latest();
        }])
        ->has('childs','>',0)
        ->active()
        ->parent()
        ->latest()->get();
        // dd($categories);

        $brands = Brand::select(['id', 'name'])->active()->latest()->get();
        $units = ProductUnit::select(['id', 'name'])->latest()->get();
        $attributes = ProductAttribute::select(['id', 'name'])->active()->latest()->get();
        return view('admin.product.create', compact('categories', 'brands', 'units', 'attributes'));
    }

    public function store(ProductRequest $request)
    {
        try {
            $product = $this->productService->createProduct($request);
            return redirect(route('products.index'))->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        setPageMeta('Product edit');
        setCreateRoute(null);
        try {
            // $categories = Category::select(['id', 'name'])->active()->latest()->get();
            $categories = Category::select(['id', 'name','parent_id'])
            ->with(['childs'=>function($query){
                $query->select(['id', 'name','parent_id'])->active()->latest();
            }])
            ->has('childs','>',0)
            ->active()
            ->parent()
            ->latest()->get();

            $brands = Brand::select(['id', 'name'])->active()->latest()->get();
            $units = ProductUnit::select(['id', 'name'])->latest()->get();
            $attributes = ProductAttribute::select(['id', 'name'])->active()->latest()->get();
            $product = $this->productService->getProductById($id)->load(['product_tags']);
            return view('admin.product.edit', compact('product', 'categories', 'brands', 'units', 'attributes'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function show($id)
    {
        setPageMeta('Product show');
        setCreateRoute(null);
        try {
            $product = $this->productService->getProductById($id);
            return view('admin.product.edit', compact('product'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(ProductRequest $request, $id)
    {
        try {
            $product = $this->productService->updateProduct($id, $request);
            return redirect(route('products.index'))->with('success', 'Product updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->productService->deleteProduct($id);
            return back()->with('success', 'Product deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadProductBarcode($id)
    {
        $product = $this->productService->get($id);
        if($product->is_variant){
             // Define an array of image file paths
            $files = [];
            foreach ($product->variants as $variant){
                $files[] = $variant->barcode_image;

            }
            // Define ZIP file name
            $zipFileName = 'sku_'.$product->sku.'_barcodes.zip';
            $zipPath = storage_path($zipFileName);
            return $this->productService->barcodeDownloadZip($zipPath,$files);

        }else{
            return  $this->productService->barcodeDownload($product->barcode_image);

        }
    }

    public function barcodeDownloadZip(Request $request){
        // dd($request->all());
        $files = [];
        if ($request->filled('product_ids')){
            $product_ids = explode(',', request('product_ids'));
            $products = Product::query()->findMany($product_ids);
        }else{
            $products = Product::query()->get();
        }

        if($products->count() > 0){
            foreach($products as $product){
                // $files[] = $product->barcode_image;
                if($product->is_variant){
                    foreach ($product->variants as $variant){
                    $files[] = $variant->barcode_image;

                    }
                }else{

                    $files[] = $product->barcode_image;
                }
            }

            // Define ZIP file name
            $zipFileName = 'barcodes.zip';
            $zipPath = storage_path($zipFileName);
            return $this->productService->barcodeDownloadZip($zipPath,$files);
        }else{
            sendFlash('No Product Found', 'error');
            return back();
        }

    }
    public function productReviews(ProductReviewsDataTable $dataTable)
    {
        setPageMeta('Product Reviews List');
        setCreateRoute(null);
        return $dataTable->render('admin.product.reviews');
    }

    public function productReviewsEdit($id)
    {
        setPageMeta('Product Review edit');
        setCreateRoute(null);
        try {

            $review = $this->reviewService->get($id)->load(['images']);
            // dd($review);
            return view('admin.product.edit-review', compact('review'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function productReviewsUpdate(ReviewRequest $request,$rId)
    {
        $request->validated();

        try {

            $this->reviewService->createOrUpdate($request,$rId);
            return redirect()->route('product.reviews',$request->product_id)->with('success', 'Review Uploaded successfully');

        }catch(\Exception $e){
            logger($e->getMessage());
            return back()->with('error', $e->getMessage());
        }

    }

    public function productReviewsDelete($id){
        try {
            $review = $this->reviewService->get($id);
            if(!$review){
                throw new \Exception('Review not found');
            }

            $res = $this->reviewService->destroy($id);
            return redirect()->route('product.reviews',$review->product_id)->with('success', 'Review Deleted successfully');
        }catch(\Exception $e){
            logger($e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function lowStockProducts(LowStockProductDataTable $dataTable)
    {
        setPageMeta('Low Stock Product List');
        setCreateRoute(null);
        return $dataTable->render('admin.product.index');
    }
}
