<?php

namespace App\Http\Controllers\Api\V1\Promotion;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\PromotionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Traits\PaginatedResourceTrait;
use App\Http\Requests\Api\V1\UserRequest;
use App\Http\Resources\Promotion\PromotionResource;
use App\Http\Resources\User\UserResource;

class PromotionController extends Controller
{
    use ApiResponse, PaginatedResourceTrait;
    public $promotionService;
    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }
    public function index()
    {
        try {
            $promotions = $this->promotionService->getActive([
                'id','title','image','message','start_date','end_date'
            ],[]);
            // $promotions = $this->promotionService->getActive([
            //     'id','title','image','message'
            // ],['promotion_items','promotion_items.category','promotion_items.product']);
            // return $promotions;
            $resource =  $this->paginatedResponse($promotions, PromotionResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
}
