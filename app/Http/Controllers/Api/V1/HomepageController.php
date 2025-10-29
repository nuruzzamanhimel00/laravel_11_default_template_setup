<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Services\PromotionService;
use App\Http\Controllers\Controller;
use App\Traits\PaginatedResourceTrait;
use App\Services\Product\ProductService;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Promotion\PromotionResource;

class HomepageController extends Controller
{
    use ApiResponse, PaginatedResourceTrait;
    public $categoryService;
    public $promotionService;
    public $productService;


    public function __construct(CategoryService $categoryService, PromotionService $promotionService,ProductService $productService)
    {
        $this->categoryService = $categoryService;
        $this->promotionService = $promotionService;
        $this->productService = $productService;
    }
    //get all active pages
    public function index()
    {
        try {

            $categories = $this->categoryService->getActiveParentCategories(['id','name','image']);


            $categoryResource = $this->paginatedResponse($categories, CategoryResource::class);

            $promotions = $this->promotionService->getActive(['id','title','message','image'],[]);

            $promotionsResource =  $this->paginatedResponse($promotions, PromotionResource::class);


            // $newArrivalProducts = $this->productService->getNewArrivalProducts(['id','name','image','purchase_price','sale_price','restaurant_sale_price','taxes'],['latest_promotion_item']);

            // $newArrivalResource =  $this->paginatedResponse($newArrivalProducts, ProductResource::class);

            $bestSellingProducts = $this->productService->getBestSellingProducts(['id','name','image','purchase_price','sale_price','restaurant_sale_price','taxes'],['latest_promotion_item']);

            $bestSellingResource = $this->paginatedResponse($bestSellingProducts, ProductResource::class);



            $apiData= [
                'categories' => $categoryResource['data'],
                'promotions' => $promotionsResource['data'],
                // 'new_arrival_products' => $newArrivalResource['data'],
                'best_selling_products' => $bestSellingResource['data']
            ];

            return $this->success($apiData,'Homepage data loaded successfully',200);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
    public function show($slugOrId)
    {
        try {
            $category = $this->categoryService->getCategory($slugOrId);
            if (!$category) {
                return $this->error('category not found', 404);
            }
            return $this->success(new CategoryResource($category));
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

}
