<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use App\Models\WarehouseStock;
use App\Mail\InvoiceCreateMail;
use App\Events\OrderNotifyEvent;
use App\Jobs\OrderAdminNotifyJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Jobs\OrderDeliveryManNotifyJob;
use Illuminate\Support\Facades\Storage;
use App\Notifications\OrderStatusNotify;

class OrderService extends BaseService
{
//    protected $fileUploadService;

    public function __construct(Order $model)
    {
        parent::__construct($model);
    }
    public function createOrUpdate(Request|array $request, int $id = null): Order
    {

        $data           = $request->all();
        $saleData = $request->only(['date','order_for','order_for_id','billing_info','shipping_info','discount_amount','global_discount','global_discount_type','sub_total','total','total_paid','payment_type','tax_amount','is_split_sale']);
        $saleData['is_split_sale'] = $request->is_split_sale == 'true' ? true : false;
        // dd($saleData);
        try {
            DB::beginTransaction();

            if ($id) {
                // // Update
                $order = $this->get($id);
                $saleData['invoice_no'] = $order->invoice_no;

                $sale = $this->updateSale($data, $saleData,$id);

                DB::commit();
                return $sale;

            } else {
                $invoice_no = $this->invoiceNoGenerate();
                $saleData['invoice_no'] = $invoice_no;
                $sale = $this->createSale($data, $saleData);

                DB::commit();
                return $sale;
            }



        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

    }

        public  function invoiceNoGenerate()
        {
            $lastSale = Order::latest('id')->first();
            $newId = $lastSale ? $lastSale->id : 1;
            return 'INV-'. rand(1000,9999).'-' . str_pad($newId, 4, '0', STR_PAD_LEFT);
        }

    // public function invoiceCreateMail($id){
    //     $sale  = Sale::latest()->with([
    //         'sale_items',
    //         'location',
    //         'outgoing_location',
    //         'customer',
    //         'order_payments',
    //         'sale_items.product',
    //         'sale_items.product_condition',
    //         'sale_items.product_variant_condition'
    //     ])->find($id);
    //     if(!is_null($sale->customer?->email)){
    //         try {
    //             Mail::to($sale->customer?->email)->send(
    //                 new InvoiceCreateMail($sale)
    //             );
    //         } catch (\Throwable $th) {
    //             throw $th;
    //         }
    //     }


    // }

    public function updateSale($data, $saleData, $id=null){


        if($data['walk_in_customer'] == 'true'){
            $customer = User::create([
                'first_name' => $data['customer']['name'],
                'phone' => $data['customer']['phone'],
                'email' => $data['customer']['email'],
                'type' => $data['order_for'],
                'password' => Hash::make('12345678')
            ]);
            if($customer){
                $saleData['order_for_id'] = $customer->id;
            }
        }

        $saleData = array_merge($saleData, [
            'payment_status' => $data['total_paid'] == $data['total'] ? Order::STATUS_PAID : (
                $data['total_paid'] < $data['total'] && $data['total_paid'] > 0 ? Order::STATUS_PARTIALLY_PAID : Order::STATUS_PENDING
            ),
            'billing_info' => json_decode($data['billing_info'], true),
            'shipping_info' => json_decode($data['shipping_info'], true),
            'order_status' => Order::STATUS_PENDING
        ]);

        // Create the purchase record
        $sale = Order::updateOrCreate(
            ['id' => $id], // ✅ Fix here
            $saleData
        );


        $sale_items_decoded = array_map(function ($item) {
            $item['data'] = json_decode($item['data'], true);
            return $item;
        }, $data['sale_items']);

        // Use the relationship to create multiple purchase_items
        $sale->order_items()->delete();
        $sale->order_items()->createMany($sale_items_decoded);


        $sale->order_payments()->delete();

        if($data['total_paid'] > 0 && $data['total_paid'] <= $data['total']){
            //create payment
            $this->makeAPayment($sale, $data);
        }
        return $sale;
    }
    public function createSale($data, $saleData){

        if($data['walk_in_customer'] == 'true'){
            $customer = User::create([
                'first_name' => $data['customer']['name'],
                'phone' => $data['customer']['phone'],
                'email' => $data['customer']['email'],
                'type' => $data['order_for'],
                'password' => Hash::make('12345678')
            ]);
            if($customer){
                $saleData['order_for_id'] = $customer->id;
            }
        }

        $saleData = array_merge($saleData, [
            'payment_status' => $data['total_paid'] == $data['total'] ? Order::STATUS_PAID : (
                $data['total_paid'] < $data['total'] && $data['total_paid'] > 0 ? Order::STATUS_PARTIALLY_PAID : Order::STATUS_PENDING
            ),
            'billing_info' => json_decode($data['billing_info'], true),
            'shipping_info' => json_decode($data['shipping_info'], true),
            'order_status' => Order::STATUS_PENDING
        ]);

        // Create the purchase record
        $sale = $this->model::create($saleData);


        $sale_items_decoded = array_map(function ($item) {
            $item['data'] = json_decode($item['data'], true);
            return $item;
        }, $data['sale_items']);

        // Use the relationship to create multiple purchase_items
        $sale->order_items()->createMany($sale_items_decoded);

        $staticStatus = Order::STATUS_PENDING;

        //---------------------- Order Status Create ----------------------
        $customStatus = [
            'title' => ucwords(str_replace('_', ' ',  $staticStatus)),
            'message' =>  'Your Order Successfully Created. Invoice id: '.$sale->invoice_no,
            'order_type'    => 'success',
            'status' => $staticStatus,
            'user_id' => auth()->user()->id,
            // 'visit_url' => route('order.status.change', $sale->id),
        ];
        $this->orderStatusCreate($sale, $customStatus);

        $customStatusText = ucwords(str_replace('_', ' ', $staticStatus));
        //-------------------admin notify on mail and notification --------------------
        $this->notifyAdmins($sale, $customStatusText);


        if($data['total_paid'] > 0 && $data['total_paid'] <= $data['total']){
            //create payment
            $this->makeAPayment($sale, $data);
        }
        return $sale;
    }


    public function orderStatusCreate(Order $order, $data ){
        $order->order_status()->create([
            'user_id' =>  $data['user_id'],
            'status' => $data['status'],
            'title' => $data['title'],
            'message' => $data['message'],
            'order_type' => $data['order_type'],
        ]);
    }

    public function paymentCreate(Order $sale, array $paymentData): void
    {

        try {
            DB::beginTransaction();
            $total_paid = $sale->total_paid + $paymentData['total_paid'];

            // Create the payment
            $this->makeAPayment($sale, $paymentData);

            // Update the total_paid amount
            $sale->total_paid = $total_paid; // Increment the total_paid amount
            $sale->payment_status = $total_paid == $sale->total ? Order::STATUS_PAID : Order::STATUS_PARTIALLY_PAID;
            $sale->save(); // Save the updated sale
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

    }

    public function paymentUpdate(Request $request, Order $sale,  OrderPayment $salePayment): void
    {

        try {
            DB::beginTransaction();
            $salePayment->amount = $request->total_paid;
            $salePayment->notes = $request->notes;
            $salePayment->payment_type = $request->payment_type;
            $salePayment->save();

            $this->updateSaleTotalPaid($sale->id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

    }

    public function updateSaleTotalPaid($sid)
    {
        $sale = Order::find($sid)->load(['order_payments']);

        $total_paid =  $sale->order_payments->sum('amount');
        $sale->total_paid = $total_paid;
        $sale->payment_status = $total_paid == $sale->total ? Order::STATUS_PAID : Order::STATUS_PARTIALLY_PAID;
        $sale->save();
    }

    public function makeAPayment(Order $sale, array $paymentData): void
    {

        // Ensure required fields exist
        if (
            // empty($paymentData['payment_info']) ||
            empty($paymentData['total_paid']) ||
            empty($paymentData['payment_type'])
        ) {
            throw new \Exception('Invalid payment data.');
        }

        // // Update stock quantity
        // if($sale->order_payments->count() == 0){
        //     $this->updateStock($sale->sale_items->toArray(), $sale->location_id);
        // }

        // Store payment record
        $sale->order_payment()->create([
            'order_id'     => $sale->id,
            'date'         => $paymentData['payment_info']['date'] ?? Carbon::now(),
            'payment_type' => $paymentData['payment_type'],
            'amount'       => $paymentData['total_paid'],
            'notes'        => $paymentData['payment_info']['notes'] ?? null,
            'account_info' => isset($paymentData['payment_info']) ? ($paymentData['payment_info']) : null, // Ensure JSON storage if necessary
        ]);
    }
     /**
     * Update stock quantity for sale items.
     *
     * @param array $saleItems
     * @throws Exception
     */
    public function updateStock( $saleItems, $location_id): void
    {

        //stock management
        foreach($saleItems as $item){

            $ProductStock = ProductStock::query()
            ->where('product_id', $item['product_id'])
            ->when(!empty($item['variant_id']), function ($query) use ($item) {
                return $query->where('variant_id', $item['variant_id']);
            })
            ->where('warehouse_id', $location_id)
            ->with(['product'])
            ->first();

              // Check if the product stock record exists and has sufficient quantity
            if ($ProductStock) {
                if ( (int)$item['quantity'] <= $ProductStock->quantity) {
                    // Update the stock quantity
                    $ProductStock->decrement('quantity', $item['quantity']);
                    //total product stock update
                    $ProductStock->product->total_stock -= $item['quantity'];
                    $ProductStock->product->save();
                } else {
                    // Handle insufficient stock (e.g., throw an exception or log an error)
                    throw new \Exception("Insufficient stock for product ID: {$item['product_id']}");
                }
            } else {
                // Handle case where product stock record does not exist
                throw new \Exception("Product stock not found for product ID: {$item['product_id']}");
            }
        }
    }

    public function delete($id){
        $sale = $this->get($id)->load(['order_items','order_payments','order_statuses']);


        try {
            DB::beginTransaction();
            // old data delete and new crete
            $sale->order_items()->delete();
            $sale->order_payments()->delete();
            $sale->order_statuses()->delete();
            $sale->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            //throw $th;
        }

    }

    public  function getActiveAllData()
    {
        $regular_users = User::query()
            ->where('status', User::STATUS_ACTIVE)
            ->where('type', User::TYPE_REGULAR_USER)
            ->get();
        $restaurants = User::query()
            ->where('status', User::STATUS_ACTIVE)
            ->where('type', User::TYPE_RESTAURANT)
            ->with(['restaurant'])
            ->get();

        return [
            'regular_users' => $regular_users,
            'restaurants' => $restaurants
        ];
    }


    /**
     * Returns the default warehouse ID
     *
     * If the warehouse_id is passed, it is returned as is.
     * Otherwise, it looks for a warehouse with is_default = 1 and returns its ID.
     * If no such warehouse is found, it returns null.
     *
     * @param Collection $warehouses
     * @param int|null $warehouse_id
     * @return int|null
     */
    public function getDefaultWarehouseId($warehouses, $warehouse_id)
    {
        return $warehouse_id ?? (
            $warehouses->where('is_default', 1)->first()->id ?? null
        );
    }

    public function getProductFromStock($request)
    {
        $search = $request->search ?? '';
        $orderFor = $request->order_for ?? '';
        $paginateNum = 10;
        $now = Carbon::now();

        // dd($now);

        $warehouseStocks = WarehouseStock::query()
            ->with([
                'product.productUnit',
                'warehouse',
                'product.productMeta',
                'product.reviews',
                'promotion_items' => function ($query) use ($orderFor , $now) {
                    return $query->with(['promotion'])->whereHas('promotion', function ($query) use ($orderFor, $now) {
                        $query->where('target_type', $orderFor)
                        ->whereDate('start_date', '<=', $now)
                        ->whereDate('end_date', '>=', $now);
                    })->latest();
                }

            ])
            ->where('stock_quantity', '>', 0)
            ->when($search, function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('sku', $search)
                        ->orWhere('barcode', $search);
                });
            })
            ->when($orderFor, function ($query) use ($orderFor) {
                $query->whereHas('product', function ($query) use ($orderFor) {
                    $query->active()->where(function ($q) use ($orderFor) {
                        $q->where('available_for', $orderFor)
                            ->orWhere('available_for', 'Both');
                    });
                });
            })
            ->paginate($paginateNum);

        return $warehouseStocks;
    }



    public function productStockFormat($ProductStocks){

        $searchItems = [];
        if($ProductStocks && count($ProductStocks) > 0){
            foreach($ProductStocks as $ProductStock){

                $searchItems[] = [
                    'id' => $ProductStock->id,
                    'product_id' => $ProductStock->product_id,
                    'variant_id' => $ProductStock->variant_id ?? null,
                    'warehouse_id' => $ProductStock->warehouse_id,
                    'product_name' => $ProductStock->product?->name,
                    'product_sku' => $ProductStock->product?->sku,
                    'product_barcode' => !is_null($ProductStock->variant_id) ?  $ProductStock->variant?->barcode : $ProductStock->product?->barcode,
                    'stock_quantity' => $ProductStock->stock_quantity ?? 0,
                    'quantity' => 0,
                    'discount' => 0,
                    'discount_type' => null,
                    'price' => $ProductStock->sale_price ?? 0,
                    'sub_total' => 0,
                    'variant_name' => null,
                    'product_image' => $ProductStock?->product?->image_url ?? null,
                    'payout_total' => 0,
                    'barcode' => !is_null($ProductStock->variant_id) ? $ProductStock?->variant?->barcode :  $ProductStock->product?->barcode,
                    'size'  =>  null,
                    'condition_name' => null,
                    'name' =>    $ProductStock->product?->name,
                ];
            }
        }
        return $searchItems;
    }

    public function deleteOrderPayment($saleId, $paymentId): bool
    {
        // Fetch the sale
        $sale = $this->get($saleId);
        // Fetch the sale payment
        $salePayment = OrderPayment::findOrFail($paymentId);
        $total_paid = $sale->total_paid  - $salePayment->amount;
        // Deduct the payment amount from the sale's total_paid
        $sale->total_paid = $total_paid;
        $sale->payment_status = $total_paid == 0 ? Order::STATUS_PENDING : Order::STATUS_PARTIALLY_PAID;
        // Save the updated sale
        if ($sale->save()) {
            // Delete the sale payment
            $salePayment->delete();
            return true; // Return true if successful
        }

        return false; // Return false if the sale could not be saved
    }


    public function statusUpdate(Request $request, $id)
    {
        $status = $request->status;
        $customStatusText = ucwords(str_replace('_', ' ', $status));
        // dd($request->all());
        try {
            DB::beginTransaction();

            $order = $this->get($id)->load([
                'order_items',
                'order_items.warehouse_stock',
                'order_items.product',
                'delivery_man'
            ]);

            switch ($status) {
                case Order::STATUS_ORDER_PLACED:

                    $deliveryMans = User::active()
                        ->where('type', User::TYPE_DELIVERY_MAN)
                        ->select('id', 'first_name', 'last_name', 'status', 'type', 'email')
                        ->get();

                    foreach ($deliveryMans as $deliveryMan) {
                        $this->notifyUser($deliveryMan, $order, $customStatusText, $status);
                    }

                    $this->updateOrderStatus($order, $status);
                    $this->orderStatusCreate($order, $this->getStatusMeta($customStatusText, $status, $order));
                    $this->handleStockUpdate($order,'sub');
                    break;

                case Order::STATUS_ORDER_PACKAGING:
                case Order::STATUS_ORDER_PACKAGED:
                    if ($order->delivery_man_id && !is_null($order->delivery_man)) {
                        $this->notifyUser($order->delivery_man, $order, $customStatusText, $status);
                    }

                    $this->updateOrderStatus($order, $status);
                    $this->orderStatusCreate($order, $this->getStatusMeta($customStatusText, $status, $order));
                    break;
                case Order::STATUS_DELIVERY_ACCEPTED:
                case Order::STATUS_DELIVERY_COLLECTED:
                case Order::STATUS_DELIVERY_DELIVERED:

                if(empty($order->delivery_man_id) && !is_null($request->delivery_man_id)){
                    $order->update([
                        'delivery_man_id' => $request->delivery_man_id,
                        'delivery_status' => $status
                    ]);
                }else{
                        $order->update([
                            'delivery_status' => $status
                        ]);
                }
                $order = $this->get($id)->load([
                    'delivery_man'
                ]);
                $this->notifyUser($order->delivery_man, $order, $customStatusText, $status);

                //-------------------admin notify on mail and notification --------------------
                $this->notifyAdmins($order, $customStatusText);

                $this->orderStatusCreate($order, $this->getStatusMeta($customStatusText, $status, $order));
                break;
                case Order::STATUS_CANCEL:
                    if($order->order_status  != Order::STATUS_PENDING){
                        $this->handleStockUpdate($order,'add');
                    }

                    if ($order->delivery_man_id && !is_null($order->delivery_man)) {
                        $this->notifyUser($order->delivery_man, $order, $customStatusText, $status);
                    }

                    $this->updateOrderStatus($order, $status);
                    $this->orderStatusCreate($order, $this->getStatusMeta($customStatusText, $status, $order));
                    break;

                default:
                    throw new \Exception("Invalid order status.");
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order status updated successfully.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    private function notifyUser($user, $order, $customStatusText, $status)
    {
        $notifyData = [
            'title' => $customStatusText,
            'message' => "Order Successfully {$customStatusText}. Invoice id: {$order->invoice_no}",
            'order_type' => 'success',
            'status' => $status,
            'user_id' => $user->id,
            'to_mail' => $user->email,
            'visit_url' => '#',
            'user' => $user,
            'subject' => "Invoice id: {$order->invoice_no} {$customStatusText} Successfully, Date: " . now()->format('d-m-Y'),
            'order_id' => $order->id,
            'notify_type' => 'order'
        ];

        dispatch(new OrderDeliveryManNotifyJob($notifyData));
        $user->notify(new OrderStatusNotify($notifyData));

        try {
            // Get the latest notification just stored in DB
            $latestNotification = $user->notifications()->latest()->first();
            broadcast(new OrderNotifyEvent($latestNotification));
        } catch (\Throwable $th) {
            //throw $th;
        }

    }

    private function getStatusMeta($customStatusText, $status, $order)
    {
        return [
            'title' => $customStatusText,
            'message' => "Order Successfully {$customStatusText}. Invoice id: " . $order->invoice_no,
            'order_type' => 'success',
            'status' => $status,
            'user_id' => auth()->id(),
        ];
    }


    public function orderStatusUpdateByUser(Request $request, $id)
    {
        $status = $request->status;
        $customStatusText = ucwords(str_replace('_', ' ', $status));

        try {
            DB::beginTransaction();
            $authUser = auth()->user();
            $order = $this->get($id)->load([
                'order_items',
                'order_items.warehouse_stock',
                'order_items.product',
                'delivery_man'
            ]);

            if($order && $order->order_status == Order::STATUS_CANCEL){
                throw new \Exception("Cannot change to {$status} for this order. Already this order is ".Order::STATUS_CANCEL);
            }
            $alreadyExists = OrderStatus::where('order_id', $order->id)
            ->where('status', $status)
            ->exists();

            if ($alreadyExists) {
                throw new \Exception("Cannot change to {$status} for this order. Already change this status.");
            }
            // dd('dd');
            $this->validateStatusChange($order, $status);

            $this->notifyAdmins($order, $customStatusText);
            $this->orderStatusCreate($order, [
                'title' => $customStatusText,
                'message' => 'Order Successfully ' . $customStatusText . '. Invoice id: ' . $order->invoice_no,
                'order_type' => 'success',
                'status' => $status,
                'user_id' => auth()->id(),
            ]);

            if($authUser->type == User::TYPE_DELIVERY_MAN){
                $order->update([
                    'delivery_man_id' => auth()->id(),
                    'delivery_status' => $status
                ]);
            }else{
                $order->update([
                    'delivery_status' => $status
                ]);
            }


            DB::commit();
            return true;

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    private function validateStatusChange($order, $status)
    {
        $authUser = auth()->user();

        $statusGroups = [
            User::TYPE_DELIVERY_MAN => [
                Order::STATUS_DELIVERY_ACCEPTED,
                Order::STATUS_DELIVERY_COLLECTED,
                Order::STATUS_DELIVERY_DELIVERED,
            ],
            User::TYPE_REGULAR_USER => [
                Order::STATUS_DELIVERY_COMPLETE
            ],
            User::TYPE_RESTAURANT => [
                Order::STATUS_DELIVERY_COMPLETE
            ],
        ];

        if (!in_array($status, $statusGroups[$authUser->type] ?? [])) {
            throw new \Exception("Invalid status for current user type");
        }
        // dd('authUser',$authUser);

        switch ($status) {
            case Order::STATUS_DELIVERY_ACCEPTED:
                if (!is_null($order->delivery_man_id)) {
                    throw new \Exception("Delivery man already assigned");
                }

                // if ($order->order_status !== Order::STATUS_ORDER_PLACED) {
                if (!in_array($order->order_status ,[Order::STATUS_ORDER_PLACED, Order::STATUS_ORDER_PACKAGING,Order::STATUS_ORDER_PACKAGED])) {
                    throw new \Exception("Order status must be " . Order::STATUS_ORDER_PLACED);
                }
                break;

            case Order::STATUS_DELIVERY_COLLECTED:

                if (
                    is_null($order->delivery_man_id)
                    || $order->delivery_status === $status
                    || $order->delivery_status !== Order::STATUS_DELIVERY_ACCEPTED
                    || $order->order_status !== Order::STATUS_ORDER_PACKAGED

                ) {
                    throw new \Exception("Cannot change to {$status} for this order");
                }

                if ($order->delivery_man_id !== auth()->id()) {
                    throw new \Exception("Unauthorized action.");
                }

                // dd('STATUS_DELIVERY_COLLECTED');
                break;

            case Order::STATUS_DELIVERY_DELIVERED:
            // case Order::STATUS_DELIVERY_COMPLETE:


                if (is_null($order->delivery_man_id)
                    || $order->order_status !== Order::STATUS_ORDER_PACKAGED
                    || $order->delivery_status === $status
                    || $order->delivery_status !== Order::STATUS_DELIVERY_COLLECTED

                ) {
                    throw new \Exception("Cannot change to {$status} for this order");
                }

                if ($order->delivery_man_id !== auth()->id()) {
                    throw new \Exception("Unauthorized action.");
                }
                // dd('delivered');
                break;

            case Order::STATUS_DELIVERY_COMPLETE:


                if (is_null($order->delivery_man_id)
                    || $order->order_status !== Order::STATUS_ORDER_PACKAGED
                    || $order->delivery_status !== Order::STATUS_DELIVERY_DELIVERED

                ) {
                    throw new \Exception("Cannot change to {$status} for this order");
                }

                if ($order->order_for_id !== auth()->id()) {
                    throw new \Exception("Unauthorized action.");
                }

                break;

            default:
                throw new \Exception("Invalid order status.");
        }
    }

    public function allAdminNotify($sale){
        $users = User::active()->where('type',User::TYPE_ADMIN)
        // ->where('id', '!=', auth()->user()->id)
        ->get();
        // dd(auth()->user()->can('Status Change Order'));
        foreach($users as $user){
            if($user->can('Status Change Order')){
                $notifyCustomData = [
                    'title' => ucwords(str_replace('_', ' ',  Order::STATUS_PENDING)),
                    'message' =>  'Order Successfully Created. Invoice id: '.$sale->invoice_no,
                    'order_type'    => 'success',
                    'status' => Order::STATUS_PENDING,
                    'user_id' => $user->id,
                    'to_mail' => $user->email,
                    // 'visit_url' => route('order.status.change', $sale->id),
                    'visit_url' => '/order/' . $sale->id . '/status-change',

                    'user'  => $user,
                    'subject' => "Invoice id: ".$sale->invoice_no." Created Successfully, Date: " . date('d-m-Y'),
                    'order_id' => $sale->id,
                    'notify_type' => 'order'

                ];

                try {
                    //mail notification
                    dispatch(new OrderAdminNotifyJob($notifyCustomData));
                    // Selected Admin Notified
                    $user->notify(new OrderStatusNotify($notifyCustomData));

                    $latestNotification = $user->notifications()->latest()->first();
                    broadcast(new OrderNotifyEvent($latestNotification));
                } catch (\Throwable $th) {
                    //throw $th;
                }

            }
        }
    }

    public function notifyAdmins($order, $customStatusText)
    {
        $admins = User::active()->where('type', User::TYPE_ADMIN)
        // ->select(['id', 'email','type','phone','first_name','last_name','status'])

        ->get();

        foreach ($admins as $admin) {
            if ($admin->can('Status Change Order')) {
                $notifyData = [
                    'title' => $customStatusText,
                    'message' => 'Order Successfully ' . $customStatusText . '. Invoice id: ' . $order->invoice_no,
                    'order_type' => 'success',
                    'status' => $customStatusText,
                    'user_id' => $admin->id,
                    'to_mail' => $admin->email,
                    'visit_url' => '/order/' . $order->id . '/status-change',
                    // 'visit_url' => route('order.status.change', $order->id),
                    'user' => $admin,
                    'subject' => "Invoice id: {$order->invoice_no} Order {$customStatusText} Successfully, Date: " . now()->format('d-m-Y'),
                    'order_id' => $order->id,
                    'notify_type' => 'order'
                ];

                dispatch(new OrderAdminNotifyJob($notifyData));
                $admin->notify(new OrderStatusNotify($notifyData));

                try {
                    // Get the latest notification just stored in DB
                    $latestNotification = $admin->notifications()->latest()->first();
                    broadcast(new OrderNotifyEvent($latestNotification));
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        }
    }



    private function handleStockMinus($order)
    {
        foreach ($order->order_items as $item) {
            if ($item->quantity <= $item->warehouse_stock->stock_quantity) {
                $item->warehouse_stock->stock_quantity -= $item->quantity;
                $item->warehouse_stock->save();

                $item->product->total_stock_quantity -= $item->quantity;
                $item->product->save();
            } else {
                throw new \Exception("Insufficient stock for product Barcode: {$item->product->barcode}");
            }
        }
    }
    /**
     * Handles stock quantity updates for order items
     *
     * @param \App\Models\Order $order The order containing items to update
     * @param string $type Operation type - 'add' to increase stock, any other value to decrease
     * @return void
     */
    private function handleStockUpdate(Order $order, string $type = 'add'): void
    {
        // dd($order, $type, $order->order_items );
        foreach ($order->order_items as $item) {
            if (!isset($item->warehouse_stock) || !isset($item->product)) {
                continue; // Skip if relationships aren't loaded
            }

            $quantity = (int)$item->quantity;
            // dd($quantity);
            // Update warehouse stock
            if ($type === 'add') {
                $item->warehouse_stock->stock_quantity += $quantity;
            } else {
                $item->warehouse_stock->stock_quantity -= $quantity;
            }
            $item->warehouse_stock->save();

            // Update product total stock
            if ($type === 'add') {
                $item->product->total_stock_quantity += $quantity;
            } else {
                $item->product->total_stock_quantity -= $quantity;
            }
            $item->product->save();
        }
    }



    private function updateOrderStatus($order, $status)
    {
        $order->order_status = $status;
        $order->save();
    }


    public function getDeliveryManOrders()
    {
        $request = request();
        $par_page = $request->get('par_page', 20);
        return $this->model
            ->with([
                'order_items',
                'customer.restaurant',
                // 'order_items.product',
                // 'order_items.warehouse',
                // 'order_items.product.productUnit:id,name,symbol,type',
                // 'order_items.product.productMeta:id,product_id,unit_value,notes,description',
            ])
            ->where('delivery_man_id',auth()->user()->id)
            ->latest() // Order by latest first
            ->paginate($par_page); // Paginate results

    }
    public function getOrder($orderId)
    {

        try {
            return Order::query()
            ->with([
                'order_items',
                'customer.restaurant',
                'order_items.product',
                'order_items.warehouse',
                'order_statuses.user',
                'order_items.product.productUnit:id,name,symbol,type',
                'order_items.product.productMeta:id,product_id,unit_value,notes,description',
            ])
            ->where(function($query){
                $query->where('delivery_man_id',auth()->user()->id)
                ->orWhere('delivery_man_id',null);
            })
            ->find($orderId);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return false;
        }
    }


    public function getUserOngoingOrders(
        $selectedFields = ['id','invoice_no','total','date','created_at']
    )
    {
        $par_page = request()->get('par_page', 20);

        return $this->model
            ->with([
                // 'order_statuses'
                // 'order_items',
                // 'customer.restaurant',
                // 'order_items.product',
                // 'order_items.warehouse'
            ])
            ->select($selectedFields)
            ->where('order_for_id',auth()->user()->id)
            ->where('order_status','!=',Order::STATUS_CANCEL)
            ->where(function($query){
                $query->whereNull('delivery_status')
                ->orWhere('delivery_status','!=',Order::STATUS_DELIVERY_COMPLETE);
            })
            // ->where('delivery_status','!=',Order::STATUS_DELIVERY_COMPLETE)
            ->latest() // Order by latest first
            ->paginate($par_page); // Paginate results
    }

    public function getUserOrders(    $selectedFields = ['id','invoice_no','total','date','created_at'])
    {
        $par_page = request()->get('par_page', 20);
        return $this->model
            // ->with([
            //     'order_items',
            //     'customer.restaurant',
            //     'order_items.product',
            //     'order_items.warehouse',
            //     'order_statuses.user'
            // ])
                ->select($selectedFields)
            ->where('order_for_id',auth()->user()->id)
            ->latest() // Order by latest first
            ->paginate($par_page); // Paginate results
    }
    public function getUserCompletedOrders(
            $selectedFields = ['id','invoice_no','total','date','created_at']
    )
    {
        $par_page = request()->get('per_page', 20); // Fixed typo 'par_page' to 'per_page'

        return $this->model
            // ->with([
            //     'order_items',
            //     'customer.restaurant',
            //     'order_items.product',
            //     'order_items.warehouse'
            // ])
                ->select($selectedFields)
            ->where('order_for_id', auth()->id()) // Simplified auth call
            ->whereNotIn('order_status', [
                Order::STATUS_PENDING,
                Order::STATUS_CANCEL
            ]) // More efficient than multiple != conditions
            ->where('delivery_status', Order::STATUS_DELIVERY_COMPLETE)
            ->latest()
            ->paginate($par_page); // Consistent variable naming (camelCase)
    }

    public function getUserOrder($orderId)
    {

        try {
            return Order::query()
            ->with([
                'order_items',
                'customer.restaurant',
                'order_items.product',
                'order_items.warehouse',
                'order_statuses.user'
            ])
            ->where('order_for_id',auth()->user()->id)
            ->find($orderId);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return false;
        }
    }

    public function allOrderRequest(
        $selectedFields = ['id','invoice_no','total','date','order_for_id','created_at']
    )
    {
        $request = request();
        $par_page = $request->get('par_page', 20);
        return $this->model
            ->with([
                'order_items',
                'customer.restaurant',
                // 'order_items',

            ])
            // ->withCount(['order_items'])
            // ->select($selectedFields)
            ->whereNull('delivery_man_id')
            ->where('order_status','!=',Order::STATUS_CANCEL)
            ->whereIn('order_status',[
                Order::STATUS_ORDER_PLACED,
                Order::STATUS_ORDER_PACKAGING,
                Order::STATUS_ORDER_PACKAGED,
            ])

            ->latest() // Order by latest first
            ->paginate($par_page); // Paginate results
    }

    public function getDeliveryManCurrentOrders(
        $selectedFields = ['id','invoice_no','total','date']
    )
    {
        $request = request();
        $par_page = $request->get('par_page', 20);
        return $this->model
            ->with([
                'order_items',
                'customer.restaurant',
                // 'order_items.product',
                // 'order_items.warehouse',
                // 'order_items.product.productUnit:id,name,symbol,type',
                // 'order_items.product.productMeta:id,product_id,unit_value,notes,description',
            ])
                // ->select($selectedFields)
            ->where('delivery_man_id',auth()->user()->id)
            ->where('order_status','!=',Order::STATUS_CANCEL)
            ->where('delivery_status','!=',Order::STATUS_DELIVERY_COMPLETE)
            ->latest() // Order by latest first
            ->paginate($par_page); // Paginate results

    }
    public function getDeliveryManPreviousOrders()
    {
        $request = request();
        $par_page = $request->get('par_page', 20);
        return $this->model
            ->with([
                'order_items',
                'customer.restaurant',
                // 'order_items.product',
                // 'order_items.warehouse',
                // 'order_items.product.productUnit:id,name,symbol,type',
                // 'order_items.product.productMeta:id,product_id,unit_value,notes,description',
            ])
            ->where('delivery_man_id',auth()->user()->id)
            ->where('order_status','!=',Order::STATUS_CANCEL)
            ->where('delivery_status',Order::STATUS_DELIVERY_COMPLETE)
            ->latest() // Order by latest first
            ->paginate($par_page); // Paginate results

    }

}
