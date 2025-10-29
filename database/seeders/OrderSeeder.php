<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    private $chunkSize = 500; // Adjust based on your system's capacity
    private $orderData = []; // Add this line
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Disable foreign key checks and events
         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
         Order::unguard();
         OrderItem::unguard();

        // Truncate tables
        Order::truncate();
        OrderItem::truncate();

        // Preload required data
        $customers = User::whereIn('type', [User::TYPE_RESTAURANT, User::TYPE_REGULAR_USER])->pluck('id')->toArray();
        $deliveryMen = User::where('type', User::TYPE_DELIVERY_MAN)->pluck('id')->toArray();
        $products = Product::select('id', 'name', 'sku', 'sale_price')->get();
        $warehouses = Warehouse::pluck('id')->toArray();

        if (empty($customers) || empty($deliveryMen) || $products->isEmpty() || empty($warehouses)) {
            throw new \Exception('Required data missing. Seed users, products, and warehouses first.');
        }

        // Precompute reusable data
        $faker = \Faker\Factory::create();

        $totalOrders = 15000 * 12; // 15k/month * 12 months
        $this->orderData = [];
        $orderItemData = [];

        for ($orderId = 1; $orderId <= $totalOrders; $orderId++) {
            $monthOffset = intval(($orderId - 1) / 15000);
            $monthStart = Carbon::now()->subMonths($monthOffset)->startOfMonth();
            $orderDate = $faker->dateTimeBetween($monthStart, $monthStart->copy()->endOfMonth());
            // Determine order status (70% completed)
            $isCompleted = $faker->boolean(70);

            // Generate order data
            $order = $this->generateOrderData(
                $faker,
                $customers,
                $deliveryMen,
                $orderDate,
                $isCompleted,
                $orderId // Use deterministic invoice number
            );
            $this->orderData[] = $order;

            // Generate order items
            $items = $this->generateOrderItems(
                $faker,
                $orderId,
                $products,
                $warehouses
            );
            $orderItemData = array_merge($orderItemData, $items);

            // Batch insert
            if ($orderId % $this->chunkSize === 0) {
                $this->insertBatch($this->orderData, $orderItemData);
                $this->orderData = [];
                $orderItemData = [];
            }
        }

        // Insert remaining records
        if (!empty($this->orderData)) {
            $this->insertBatch($this->orderData, $orderItemData);
        }

        // Re-enable checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Order::reguard();
        OrderItem::reguard();


        // // $monthOffset = 0; $monthOffset < 12; $monthOffset++
        // // foreach ([0, 12] as $monthOffset) {
        // for ($monthOffset = 0; $monthOffset < 12; $monthOffset++) {
        //     $monthStart = Carbon::now()->subMonths($monthOffset)->startOfMonth();
        //     $monthEnd = $monthStart->copy()->endOfMonth();

        //     // Generate orders for the month
        //     $ordersPerMonth = 15000;
        //     for ($i = 0; $i < $ordersPerMonth; $i++) {
        //         // Generate order date within the month
        //         $orderDate = $faker->dateTimeBetween($monthStart, $monthEnd);

        //         // Determine order status (70% completed)
        //         $isCompleted = $faker->boolean(70);

        //         // Generate invoice number
        //         $invoiceNo = 'INV-' . $monthStart->format('Ym') . '-'.'-'. $faker->unique()->numberBetween(100000, 999999);
        //         // 'INV-' . $this->faker->unique()->numberBetween(100000, 999999),

        //          // Create order
        //          $order = $this->createOrder(
        //             $faker,
        //             $customers,
        //             $deliveryMen,
        //             $invoiceNo,
        //             $orderDate,
        //             $isCompleted
        //         );

        //          // Create order items
        //          $this->createOrderItems(
        //             $faker,
        //             $order,
        //             $products,
        //             $warehouses
        //         );

        //         // Update order totals
        //         $this->updateOrderTotals($order, $isCompleted);
        //     }
        // }
    }

    private function generateOrderData($faker, $customers, $deliveryMen, $orderDate, $isCompleted, $orderId)
    {
        $invoiceNo = 'INV-' . $orderDate->format('Ym') . '-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
        $customerType = $faker->randomElement(['restaurant', 'regular_user']);
        // $statuses = $this->getStatuses($isCompleted, $faker);
        $statuses = $this->getStatuses($faker, $isCompleted);

        return [
            'invoice_no' => $invoiceNo,
            'date' => $orderDate->format('Y-m-d'),
            'order_for_id' => $faker->randomElement($customers),
            'order_for' => $customerType,
            'delivery_man_id' => $isCompleted ? $faker->randomElement($deliveryMen) : null,
            'order_status' => $statuses['order'],
            'delivery_status' => $statuses['delivery'],
            'payment_status' => $statuses['payment'],
            'billing_info' => json_encode([
                'name' => $faker->name,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
            ]),
            'shipping_info' => json_encode([
                'name' => $faker->name,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
            ]),
            'payment_type' => $faker->randomElement(['cash', 'card', 'online']),
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function generateOrderItems($faker, $orderId, $products, $warehouses)
    {
        $items = [];
        $itemCount = $faker->numberBetween(1, 5);
        $subTotal = 0;

        for ($j = 0; $j < $itemCount; $j++) {
            $product = $products->random();
            $quantity = $faker->numberBetween(1, 5);
            $price = $product->sale_price ?? $faker->numberBetween(30, 50);
            $subTotal += $quantity * $price;

            $items[] = [
                'order_id' => $orderId,
                'product_id' => $product->id,
                'product_variant_id' => null,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'quantity' => $quantity,
                'price' => $price,
                'sub_total' => $quantity * $price,
                'warehouse_id' => $faker->randomElement($warehouses),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Calculate totals in PHP (to avoid additional queries)
        $tax = $subTotal * 0.10;
        $discount = $subTotal * 0.05;
        $total = $subTotal + $tax - $discount;
        $globalDiscount = $faker->boolean ? $subTotal * 0.02 : 0;
        $total -= $globalDiscount;

        // Update order totals (will be inserted with order data)
        $orderDataIndex = count($this->orderData) - 1;
        $paymentStatus = $this->orderData[$orderDataIndex]['payment_status']; // Get payment status from order data

        $this->orderData[$orderDataIndex]['sub_total'] = $subTotal;
        $this->orderData[$orderDataIndex]['tax_amount'] = $tax;
        $this->orderData[$orderDataIndex]['discount_amount'] = $discount;
        $this->orderData[$orderDataIndex]['total'] = $total;
        $this->orderData[$orderDataIndex]['total_paid'] = $this->calculateTotalPaid($total, $paymentStatus);
        $this->orderData[$orderDataIndex]['global_discount'] = $globalDiscount;
        $this->orderData[$orderDataIndex]['global_discount_type'] = $globalDiscount > 0 ? 'percent' : null;

        return $items;
    }

    // private function getStatuses($isCompleted, $faker)
    // {
    //     if ($isCompleted) {
    //         return [
    //             'order' => Order::STATUS_ORDER_PACKAGED,
    //             'delivery' => Order::STATUS_DELIVERY_COMPLETE,
    //             'payment' => Order::STATUS_PAID,
    //         ];
    //     }

    //     return [
    //         'order' => $faker->randomElement([
    //             Order::STATUS_ORDER_PLACED,
    //             Order::STATUS_ORDER_PACKAGING,
    //             Order::STATUS_ORDER_PACKAGED,
    //         ]),
    //         'delivery' => $faker->randomElement([
    //             Order::STATUS_DELIVERY_ACCEPTED,
    //             Order::STATUS_DELIVERY_COLLECTED,
    //             Order::STATUS_DELIVERY_DELIVERED,
    //         ]),
    //         'payment' => $faker->randomElement([
    //             Order::STATUS_PENDING,
    //             Order::STATUS_PARTIALLY_PAID,
    //             Order::STATUS_CANCEL,
    //         ]),
    //     ];
    // }

    private function getStatuses($faker, $isCompleted) // Add $faker parameter
    {
        if ($isCompleted) {
            return [
                'order' => Order::STATUS_ORDER_PACKAGED,
                'delivery' => Order::STATUS_DELIVERY_COMPLETE,
                'payment' => Order::STATUS_PAID,
            ];
        }

        return [
            'order' => $faker->randomElement([
                Order::STATUS_ORDER_PLACED,
                Order::STATUS_ORDER_PACKAGING,
                Order::STATUS_ORDER_PACKAGED,
            ]),
            'delivery' => $faker->randomElement([
                Order::STATUS_DELIVERY_ACCEPTED,
                Order::STATUS_DELIVERY_COLLECTED,
                Order::STATUS_DELIVERY_DELIVERED,
            ]),
            'payment' => $faker->randomElement([
                Order::STATUS_PENDING,
                Order::STATUS_PARTIALLY_PAID,
                Order::STATUS_CANCEL,
            ]),
        ];
    }

    private function calculateTotalPaid($total, $paymentStatus)
    {
        if ($paymentStatus === Order::STATUS_PAID) {
            return $total;
        } elseif ($paymentStatus === Order::STATUS_PARTIALLY_PAID) {
            return $total * 0.5;
        }
        return 0;
    }

    private function insertBatch($orders, $orderItems)
    {
        // Insert orders
        DB::table('orders')->insert($orders);

        // Insert order items
        DB::table('order_items')->insert($orderItems);
    }

    private function createOrder($faker, $customers, $deliveryMen, $invoiceNo, $orderDate, $isCompleted)
    {
        // Determine statuses
        if ($isCompleted) {
            $orderStatus = Order::STATUS_ORDER_PACKAGED;
            $deliveryStatus = Order::STATUS_DELIVERY_COMPLETE;
            $paymentStatus = Order::STATUS_PAID;
        } else {
            $orderStatus = $faker->randomElement([
                Order::STATUS_ORDER_PLACED,
                Order::STATUS_ORDER_PACKAGING,
                Order::STATUS_ORDER_PACKAGED,
            ]);
            $deliveryStatus = $faker->randomElement([
                Order::STATUS_DELIVERY_ACCEPTED,
                Order::STATUS_DELIVERY_COLLECTED,
                Order::STATUS_DELIVERY_DELIVERED,
            ]);
            $paymentStatus = $faker->randomElement([
                Order::STATUS_PENDING,
                Order::STATUS_PARTIALLY_PAID,
                Order::STATUS_CANCEL,
            ]);
        }

        return Order::create([
            'invoice_no' => $invoiceNo,
            'date' => $orderDate->format('Y-m-d'),
            'order_for_id' => $faker->randomElement($customers),
            'order_for' => $faker->randomElement(['restaurant', 'regular_user']),
            'delivery_man_id' => $isCompleted ? $faker->randomElement($deliveryMen) : null,
            'order_status' => $orderStatus,
            'delivery_status' => $deliveryStatus,
            'payment_status' => $paymentStatus,
            'billing_info' => [
                'name' => $faker->name,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
            ],
            'shipping_info' => [
                'name' => $faker->name,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
            ],
            'payment_type' => $faker->randomElement(['cash', 'card', 'online']),
            'created_by' => 1, // Assuming admin user ID 1 exists
            'updated_by' => 1,
        ]);
    }

    private function createOrderItems($faker, $order, $products, $warehouses)
    {
        $itemCount = $faker->numberBetween(1, 5);
        $subTotal = 0;

        for ($j = 0; $j < $itemCount; $j++) {
            $product = $products->random();
            $variant = null;

            $quantity = $faker->numberBetween(1, 5);
            $price = $product->sale_price ?? $faker->numberBetween(30,50);
            $itemSubTotal = $quantity * $price;
            // restaurant_sale_price
            // sale_price
            OrderItem::create([
                'order_id'           => $order->id,
                'product_id'         => $product->id,
                'product_variant_id' => $variant?->id,
                'product_name'       => $product->name,
                'product_sku'        => $variant?->sku ?? $product->sku,
                'quantity'           => $quantity,
                'price'              => $price,
                'sub_total'          => $itemSubTotal,
                'warehouse_id'       => $faker->randomElement($warehouses),
            ]);

            $subTotal += $itemSubTotal;
        }

        $order->sub_total = $subTotal;
    }

    private function updateOrderTotals($order, $isCompleted)
    {
        // Calculate order totals
        $taxRate = 0.10; // 10% tax
        $discountRate = 0.05; // 5% discount

        $order->tax_amount = $order->sub_total * $taxRate;
        $order->discount_amount = $order->sub_total * $discountRate;
        $order->total = $order->sub_total + $order->tax_amount - $order->discount_amount;

        // Set payment amounts
        if ($order->payment_status === Order::STATUS_PAID) {
            $order->total_paid = $order->total;
        } elseif ($order->payment_status === Order::STATUS_PARTIALLY_PAID) {
            $order->total_paid = $order->total * 0.5; // 50% paid
        } else {
            $order->total_paid = 0;
        }

        // Add random global discount for some orders
        if (rand(0, 1)) {
            $order->global_discount = $order->sub_total * 0.02; // 2% global discount
            $order->global_discount_type = 'percent';
            $order->total -= $order->global_discount;
        }

        $order->save();
    }
}
