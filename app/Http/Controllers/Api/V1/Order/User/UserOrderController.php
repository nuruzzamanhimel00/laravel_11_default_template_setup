<?php

namespace App\Http\Controllers\Api\V1\Order\User;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserVerify;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Laravel\Passport\Token;
use Illuminate\Http\Request;
use App\Services\OrderService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\RefreshToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Traits\PaginatedResourceTrait;
use App\Services\DeliveryChargeService;
use App\Http\Resources\Order\OrderResource;
use App\Notifications\EmailVerifyNotifyMail;
use App\Http\Resources\Order\OrderHistoriesResource;

class UserOrderController
{
    use ApiResponse, PaginatedResourceTrait;
    public $promotionService;
    protected $orderService;
    protected $deliveryChargeService;

    public function __construct(OrderService $orderService,  DeliveryChargeService $deliveryChargeService)
    {
        $this->orderService = $orderService;
        $this->deliveryChargeService   = $deliveryChargeService;


    }
    public function ongoingOrders()
    {
        try {
            $orders = $this->orderService->getUserOngoingOrders();
            // dd($orders);
            // return $orders;

            $resource =  $this->paginatedResponse($orders, OrderResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
    public function completedOrders()
    {
        try {
            $orders = $this->orderService->getUserCompletedOrders();
            // return $orders;

            $resource =  $this->paginatedResponse($orders, OrderResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $Order = $this->orderService->getUserOrder($id)->load(['order_items.product.productUnit']);
            // return $Order;
            if (!$Order) {
                return $this->error('Order not found', 404);
            }
            $deliveryCharge = $this->deliveryChargeService->getDeliveryCharge(['id', 'title', 'cost']);
            $deliveryCost = !$deliveryCharge || empty($deliveryCharge) ? 0 : addCurrency( $deliveryCharge->cost);
            $orderData = [
                'order' => new OrderResource($Order),
                'delivery_cost' => $deliveryCost
            ];
            // dd($Order);
            return $this->success($orderData);
            // return $this->success(new OrderResource($Order));
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    public function statusChange(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
        ]);

        try {
            $this->orderService->orderStatusUpdateByUser($request, $id);
            return $this->success(null, 'Status updated successfully');
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    public function userOrders()
    {
        try {
            $orders = $this->orderService->getUserOrders();
            // return $orders;

            $resource =  $this->paginatedResponse($orders, OrderResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    public function orderPaymentSummery(){
        return response()->json(getUserOrderSummary());
    }

}
