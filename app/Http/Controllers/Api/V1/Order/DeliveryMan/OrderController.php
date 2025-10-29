<?php

namespace App\Http\Controllers\Api\V1\Order\DeliveryMan;

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
use App\Http\Resources\Order\DeliveryManOrderResource;

class OrderController
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
    public function index()
    {
        try {
            $orders = $this->orderService->getDeliveryManOrders();

            $resource =  $this->paginatedResponse($orders, DeliveryManOrderResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $Order = $this->orderService->getOrder($id);
            // return $Order;
            if (!$Order) {
                return $this->error('Order not found', 404);
            }

            $deliveryCharge = $this->deliveryChargeService->getDeliveryCharge(['id', 'title', 'cost']);
            $deliveryCost = !$deliveryCharge || empty($deliveryCharge) ? 0 : (float) $deliveryCharge->cost;
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
    public function orderHistories($id)
    {
        try {
            $Order = $this->orderService->getOrder($id);
            // return $Order;
            if (!$Order) {
                return $this->error('Order not found', 404);
            }
            return $this->success(OrderHistoriesResource::collection($Order->order_statuses));
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

    public function orderRequest()
    {
        try {
            $orders = $this->orderService->allOrderRequest();
            // return $orders;
            // dd($orders);
            $resource =  $this->paginatedResponse($orders, DeliveryManOrderResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    public function getCurrentOrders()
    {
        try {
            $orders = $this->orderService->getDeliveryManCurrentOrders();
            // return $orders;

            $resource =  $this->paginatedResponse($orders, DeliveryManOrderResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
    public function getPreviousOrders()
    {
        try {
            $orders = $this->orderService->getDeliveryManPreviousOrders();
            // return $orders;

            $resource =  $this->paginatedResponse($orders, DeliveryManOrderResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
}
