<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Services\PromotionService;
use App\Http\Controllers\Controller;
use App\Traits\PaginatedResourceTrait;
use App\Services\DeliveryChargeService;
use App\Services\Product\ProductService;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Promotion\PromotionResource;
use App\Http\Resources\DeliveryCharge\DeliveryChargeResource;

class DeliveryChargeController extends Controller
{
    use ApiResponse, PaginatedResourceTrait;

    protected $deliveryChargeService;



    public function __construct(DeliveryChargeService $deliveryChargeService)
    {
        $this->deliveryChargeService   = $deliveryChargeService;
    }
    //get all active pages
    public function index()
    {
        try {

            $deliveryCharges = $this->deliveryChargeService->getActive(['id','title','cost']);
            // $deliveryChargesResource = $this->paginatedResponse($deliveryCharges, DeliveryChargeResource::class);


            return $this->success($deliveryCharges,'Delivery charges loaded successfully',200);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
    public function getDeliveryCharge()
    {
        try {

            $deliveryCharge = $this->deliveryChargeService->getDeliveryCharge(['id','title','cost']);
            if($deliveryCharge){

                return $this->success($deliveryCharge,'Delivery charge loaded successfully',200);
            }
                return $this->error('Delivery charge not found',404);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }


}
