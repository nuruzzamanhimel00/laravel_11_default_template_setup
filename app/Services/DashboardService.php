<?php
namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Order;

class DashboardService
{
    // public function getDashboardData(Request $request)
    // {
    //     $now = Carbon::now();
    //      // Default to current month if no date range specified
    //      $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
    //      $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
    //       // Convert strings to Carbon instances for manipulation
    //     $startDateCarbon = Carbon::parse($startDate);
    //     $endDateCarbon = Carbon::parse($endDate);

    //     // Set the dateRange for display
    //     $dateRange = [
    //         'start' => $startDate,
    //         'end' => $endDate,
    //     ];

    //     $sixMonthsAgo = $now->copy()->subMonths(6);
    //     $yearStart = $now->copy()->startOfYear();
    //     $lastMonthStartDate = $now->copy()->subMonth(1)->startOfMonth()->format('Y-m-d');
    //     $lastMonthEndDate = $now->copy()->subMonth(1)->endOfMonth()->format('Y-m-d');
    //     $currentMonthStartDate = $now->copy()->startOfMonth()->format('Y-m-d');
    //     $currentMonthEndDate = $now->copy()->endOfMonth()->format('Y-m-d');
    //     // Basic counts with optimized queries instead of loading everything
    //     $totalCustomers = User::where('type', User::TYPE_REGULAR_USER)->count();
    //     $totalRestaurants = User::where('type', User::TYPE_RESTAURANT)->count();
    //     $totalSuppliers = User::where('type', User::TYPE_SUPPLIER)->count();
    //     // last month total sales
    //     $lastMonthTotalSales = Order::delivered()->where('updated_at', '>=', $lastMonthStartDate)->where('updated_at', '<=', $lastMonthEndDate)->sum('total_paid');
    //     // // Build base query with date range
    //     // $ordersQuery = Order::query()
    //     //     ->whereNotNull('date');

    //     // // $ordersQuery = Order::query()
    //     // //     ->whereNotNull('date')
    //     // //     ->where('date', '>=', $startDate);

    //     // // Apply marketplace filter if provided
    //     // if ($marketplaceFilter) {
    //     //     $ordersQuery->where('marketplace', $marketplaceFilter);
    //     // }

    //     //  // Get total count without loading all data
    //     // $totalOrders = (clone $ordersQuery)->count();

    //     // // Get aggregated data with a single query
    //     // $orderTotals = (clone $ordersQuery)->selectRaw('
    //     //     SUM(profit) as total_profit,
    //     //     SUM(marketplace_fee) as total_marketplace_fee,
    //     //     SUM(we_shipping) as total_we_shipping_fee,
    //     //     SUM(marketplace_shipping) as total_marketplace_shipping_fee,
    //     //     SUM(prep_cost) as total_prep_cost,
    //     //     SUM(additional_fee) as total_additional_fee,
    //     //     SUM(quantity) as total_sale_quantity,
    //     //     SUM(product_price) as total_sale_price
    //     // ')->first();

    //     // // Format currency values
    //     // $formattedTotals = [
    //     //     'total_order_profit' => $orderTotals->total_profit ?? 0),
    //     //     'total_marketplace_fee' => $orderTotals->total_marketplace_fee ?? 0),
    //     //     'total_we_shipping_fee' => $orderTotals->total_we_shipping_fee ?? 0),
    //     //     'total_marketplace_shipping_fee' => $orderTotals->total_marketplace_shipping_fee ?? 0),
    //     //     'total_prep_cost' => $orderTotals->total_prep_cost ?? 0),
    //     //     'total_additional_fee' => $orderTotals->total_additional_fee ?? 0),
    //     //     'total_sale_quantity' => $orderTotals->total_sale_quantity ?? 0,
    //     //     'total_sale_price' => $orderTotals->total_sale_price ?? 0),
    //     // ];

    //     // // Generate month labels for last 12 months
    //     // $monthLabels = [];
    //     // for ($i = 1; $i <= 12; $i++) {
    //     //     $monthLabels[] = Carbon::now()->subMonths(12)->addMonths($i)->format('M Y');
    //     // }

    //     // Initialize data array
    //     $data = [
    //         'total_customers' => $totalCustomers,
    //         'total_restaurants' => $totalRestaurants,
    //         'total_suppliers' => $totalSuppliers,
    //         'last_month_sales' =>$lastMonthTotalSales)
    //     ];

    //     // // Merge in the formatted totals
    //     // $data = array_merge($data, $formattedTotals);

    //     // // Get data for monthly analytics with a single optimized query
    //     // $monthlyAnalytics = $this->getMonthlyAnalytics($startDate, $endDate, $marketplaceFilter);

    //     // // Prepare data for charts
    //     // $data = array_merge($data, $this->prepareMonthlyAnalyticsData($monthlyAnalytics, $monthLabels));

    //     // // Get order status and marketplace data
    //     // $data = array_merge($data, $this->getOrderStatusData($marketplaceFilter));

    //     // // Get marketplace distribution data (only if not filtered by marketplace)
    //     // // if (!$marketplaceFilter) {
    //     // //     $data = array_merge($data, $this->getOrderMarketplaceData());
    //     // // }
    //     // $data = array_merge($data, $this->getOrderMarketplaceData());

    //     // // Add period-specific data
    //     // $data = array_merge($data, $this->getPeriodSpecificData($marketplaceFilter));

    //     return $data;
    // }

    /**
     * Get monthly analytics data for the past 12 months
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string|null $marketplaceFilter
     * @return Collection
     */
    protected function getMonthlyAnalytics($startDate, $endDate, $marketplaceFilter = null)
    {
        $query = Order::query()
            ->selectRaw('
                DATE_FORMAT(date, "%b %Y") as month_year,
                COUNT(*) as order_count,
                SUM(quantity) as order_quantity,
                SUM(profit) as total_profit,
                SUM(prep_cost) as total_prep_cost,
                SUM(additional_fee) as total_additional_fee,
                SUM(marketplace_fee) as total_marketplace_fee,
                SUM(we_shipping) as total_we_shipping_fee,
                SUM(marketplace_shipping) as total_marketplace_shipping_fee,
                SUM(product_price) as total_product_price
            ')
            ->whereNotNull('date')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('month_year');

        if ($marketplaceFilter) {
            $query->where('marketplace', $marketplaceFilter);
        }

        return $query->get();
    }

    /**
     * Prepare monthly analytics data for charts
     *
     * @param Collection $monthlyData
     * @param array $monthLabels
     * @return array
     */
    protected function prepareMonthlyAnalyticsData($monthlyData, $monthLabels)
    {
        // Create lookup for monthly data
        $monthlyLookup = $monthlyData->keyBy('month_year');

        // Initialize arrays for different metrics
        $metrics = [
            'order_monthly_analytics' => [],
            'order_qty_monthly_analytics' => [],
            'order_monthly_analytics_profit' => [],
            'order_monthly_analytics_prep_cost' => [],
            'order_monthly_analytics_additional_fee' => [],
            'order_monthly_analytics_marketplace_fee' => [],
            'order_monthly_analytics_we_shipping_fee' => [],
            'order_monthly_analytics_marketplace_shipping_fee' => [],
            'order_monthly_analytics_sale_price' => [],
        ];

        // Populate metrics for each month
        foreach ($monthLabels as $label) {
            $monthData = $monthlyLookup->get($label);

            $metrics['order_monthly_analytics'][] = $monthData ? $monthData->order_count : 0;
            $metrics['order_qty_monthly_analytics'][] = $monthData ? $monthData->order_quantity : 0;
            $metrics['order_monthly_analytics_profit'][] = $monthData ? $monthData->total_profit : 0;
            $metrics['order_monthly_analytics_prep_cost'][] = $monthData ? $monthData->total_prep_cost : 0;
            $metrics['order_monthly_analytics_additional_fee'][] = $monthData ? $monthData->total_additional_fee : 0;
            $metrics['order_monthly_analytics_marketplace_fee'][] = $monthData ? $monthData->total_marketplace_fee : 0;
            $metrics['order_monthly_analytics_we_shipping_fee'][] = $monthData ? $monthData->total_we_shipping_fee : 0;
            $metrics['order_monthly_analytics_marketplace_shipping_fee'][] = $monthData ? $monthData->total_marketplace_shipping_fee : 0;
            $metrics['order_monthly_analytics_sale_price'][] = $monthData ? $monthData->total_product_price : 0;
        }

        return $metrics;
    }

    /**
     * Get order status distribution data
     *
     * @param string|null $marketplaceFilter
     * @return array
     */
    protected function getOrderStatusData($marketplaceFilter = null)
    {
        $query = Order::query()
            ->selectRaw('
                tracking_status,
                COUNT(*) as status_count
            ')
            ->whereNotNull('date')
            ->where('date', '>=', Carbon::now()->subMonths(12))
            ->groupBy('tracking_status');

        if ($marketplaceFilter) {
            $query->where('marketplace', $marketplaceFilter);
        }

        $statusData = $query->get();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($statusData as $status) {
            $statusKey = $status->tracking_status;
            $labels[] = Str::title($statusKey);
            $data[] = $status->status_count;

            // Assign colors based on status
            $colors[] = $this->getStatusColor($statusKey);
        }

        return [
            'tracking_status_labels' => $labels,
            'tracking_status_data' => $data,
            'tracking_status_colors' => $colors,
        ];
    }

    /**
     * Get marketplace distribution data
     *
     * @return array
     */
    protected function getOrderMarketplaceData()
    {
        $marketplaceData = Order::query()
            ->selectRaw('
                marketplace,
                COUNT(*) as marketplace_count
            ')
            ->whereNotNull('date')
            ->where('date', '>=', Carbon::now()->subMonths(12))
            ->groupBy('marketplace')
            ->get();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($marketplaceData as $item) {
            $marketplaceKey = $item->marketplace;
            $labels[] = Str::title($marketplaceKey);
            $data[] = $item->marketplace_count;

            // Assign colors based on marketplace
            $colors[] = $this->getMarketplaceColor($marketplaceKey);
        }

        return [
            'marketplace_labels' => $labels,
            'marketplace_data' => $data,
            'marketplace_colors' => $colors,
        ];
    }

    /**
     * Get period-specific data (this month, 6 months, this year)
     *
     * @param string|null $marketplaceFilter
     * @return array
     */
    protected function getPeriodSpecificData($marketplaceFilter = null)
    {
        // Build base query
        $baseQuery = Order::query()->whereNotNull('date');

        if ($marketplaceFilter) {
            $baseQuery->where('marketplace', $marketplaceFilter);
        }

        // This month data
        $thisMonth = (clone $baseQuery)
            ->whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month);

        $thisMonthCount = $thisMonth->count();
        $thisMonthProfit = $thisMonth->sum('profit');

        // Last six months data
        $lastSixMonths = (clone $baseQuery)
            ->where('date', '>=', Carbon::now()->subMonths(6));

        $lastSixMonthsCount = $lastSixMonths->count();
        $lastSixMonthsProfit = $lastSixMonths->sum('profit');

        // This year data
        $thisYear = (clone $baseQuery)
            ->whereYear('date', Carbon::now()->year);

        $thisYearCount = $thisYear->count();
        $thisYearProfit = $thisYear->sum('profit');

        return [
            'order_this_month' => $thisMonthCount,
            'order_last_six_month' => $lastSixMonthsCount,
            'order_this_year' => $thisYearCount,
            'order_this_month_profit' => $thisMonthProfit,
            'order_last_six_month_profit' => $lastSixMonthsProfit,
            'order_this_year_profit' => $thisYearProfit,
        ];
    }

    /**
     * Get color for order status
     *
     * @param string $status
     * @return string
     */
    protected function getStatusColor($status)
    {
        $colors = [
            Order::STATUS_COMPLETED => 'rgba(0,255,0,0.7)',
            Order::STATUS_CANCELLED => 'rgba(255,0,0,0.8)',
            Order::STATUS_REFUNDED => 'rgba(255,255,0,0.8)',
            Order::STATUS_FAILED => 'rgba(255,0,255,0.8)',
            Order::STATUS_PENDING => 'rgba(0,0,255,0.5)',
            Order::STATUS_PROCESSING => 'rgba(0,255,255,0.8)',
        ];

        return $colors[$status] ?? 'rgba('.rand(0,255).','.rand(0,255).','.rand(0,255).',0.8)';
    }

    /**
     * Get color for marketplace
     *
     * @param string $marketplace
     * @return string
     */
    protected function getMarketplaceColor($marketplace)
    {
        $colors = [
            Order::MARKETPLACE_AMAZON => 'rgba(0,0,0,0.8)',
            Order::MARKETPLACE_EBAY => 'rgba(0,0,255,0.8)',
            Order::MARKETPLACE_SHOPIFY => 'rgb(94,142,62)',
        ];

        return $colors[$marketplace] ?? 'rgba('.rand(0,255).','.rand(0,255).','.rand(0,255).',0.8)';
    }


    // ==================== new dashboard ==================== //
    public function getDashboardData(Request $request){
        $now = Carbon::now();
        // Default to current month if no date range specified
        $startDate = $request->input('start_date', $now->copy()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', $now->copy()->endOfMonth()->format('Y-m-d'));
        $lastMonthStartDate = $now->copy()->subMonth(1)->startOfMonth()->format('Y-m-d');
        $lastMonthEndDate = $now->copy()->subMonth(1)->endOfMonth()->format('Y-m-d');


        // Set the dateRange for display
        $dateRange = [
            'start' => $startDate,
            'end' => $endDate,
        ];

        $totalCustomers = User::where('type', User::TYPE_REGULAR_USER)->count();
        $totalRestaurants = User::where('type', User::TYPE_RESTAURANT)->count();
        $totalSuppliers = User::where('type', User::TYPE_SUPPLIER)->count();
        // last month total sales
        $lastMonthTotalSales = Order::delivered()->where('date', '>=', $lastMonthStartDate)->where('date', '<=', $lastMonthEndDate)->sum('total_paid');

         // Get monthly sales data for chart
         $monthlySales = $this->getMonthlySalesData($request);

         // Get top selling products
        $topProducts = $this->getTopSellingProducts($request);

         // Get order statistics default current month
        $orderStats = $this->getOrderStatistics($request);

        // Get top selling products
        $topProducts = $this->getTopSellingProducts($request);

        // Get payment status breakdown
        $paymentStatusData = $this->getPaymentStatusData($request);

        // Get delivery status breakdown
        $deliveryStatusData = $this->getDeliveryStatusData($request);

         // Get daily sales for the selected period
        $dailySales = $this->getDailySalesData($request);
        $data = [
            'total_customers' => $totalCustomers,
            'total_restaurants' => $totalRestaurants,
            'total_suppliers' => $totalSuppliers,
            'last_month_sales' => $lastMonthTotalSales,
            'dateRange' => $dateRange,
            'monthlySales' => $monthlySales,
            'orderStats' => $orderStats,
            'topProducts' => $topProducts,
            'paymentStatusData' => $paymentStatusData,
            'deliveryStatusData' => $deliveryStatusData,
            'dailySales' => $dailySales,
        ];
        return $data;
    }

    private function getMonthlySalesData(Request $request)
    {
        [$startDate, $endDate] = $this->getPeriod($request, 'sales_period', 'year');

        $monthlySales = Order::select(
            DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
            DB::raw('SUM(total_paid) as total_sales'),
            DB::raw('count(*) as order_count')
        )
        ->delivered()
        ->whereBetween(DB::raw('STR_TO_DATE(date, "%Y-%m-%d")'), [$startDate, $endDate])
        ->groupBy('month')
        ->orderBy('month')
        ->get();


        // Format data for Chart.js
        $labels = [];
        $salesData = [];
        $orderCountData = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $monthName = $currentDate->format('Y-m');
            $displayName = $currentDate->format('M Y');

            // $monthData = $monthlySales->where('month', $monthName)->first();
            $monthData = $monthlySales->first(function ($item) use ($monthName) {
                return $item->month == $monthName;
            });
            $labels[] = $displayName;
            $salesData[] = $monthData ? $monthData->total_sales : 0;
            $orderCountData[] = $monthData ? $monthData->order_count : 0;

            $currentDate->addMonth();
        }

        return [
            'labels' => $labels,
            'sales' => $salesData,
            'orders' => $orderCountData
        ];
    }

    private function getOrderStatistics(Request $request)
    {
        [$startDate, $endDate] = $this->getPeriod($request, 'order_stat_period', 'month');
        $totalOrders = Order::delivered()->whereBetween(DB::raw('STR_TO_DATE(date, "%Y-%m-%d")'), [$startDate, $endDate])
            ->count();

        $totalSales = Order::delivered()->whereBetween(DB::raw('STR_TO_DATE(date, "%Y-%m-%d")'), [$startDate, $endDate])
            ->sum('total_paid');

        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        $totalItems = OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
            $query->delivered()->whereBetween(DB::raw('STR_TO_DATE(date, "%Y-%m-%d")'), [$startDate, $endDate]);
        })->sum('quantity');

        return [
            'total_orders'        => $totalOrders,
            'total_sales'         => $totalSales,
            'average_order_value' => $averageOrderValue,
            'total_items'         => $totalItems
        ];
    }

    private function getTopSellingProducts(Request $request)
    {
        // [$startDate, $endDate] = $this->getDateRange($request);
        $now = Carbon::now();
        // last 12 month sales
        $startDate = $now->copy()->subMonths(12)->startOfMonth();
        $endDate = $now->copy()->endOfMonth();

        return OrderItem::select(
            'product_id',
            'product_name',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(sub_total) as item_total')
        )
        ->whereHas('order', function ($query) use ($startDate, $endDate) {
            $query->delivered()->whereBetween(DB::raw('STR_TO_DATE(date, "%Y-%m-%d")'), [$startDate, $endDate]);
        })
        ->groupBy('product_id', 'product_name')
        ->orderBy('total_quantity', 'desc')
        ->limit(10)
        ->get();
    }

    private function getPaymentStatusData(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);
        return Order::select(
            'payment_status',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_paid) as total_amount')
        )
        // ->delivered()
        ->whereBetween(DB::raw('STR_TO_DATE(date, "%Y-%m-%d")'), [$startDate, $endDate])
        ->where('payment_status', '!=', null)
        ->groupBy('payment_status')
        ->get();
    }

    private function getDeliveryStatusData(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);
        return Order::select(
            'delivery_status',
            DB::raw('COUNT(*) as count')
        )
        // ->delivered()
        ->whereBetween(DB::raw('STR_TO_DATE(date, "%Y-%m-%d")'), [$startDate, $endDate])
        ->where('delivery_status', '!=', null)
        ->groupBy('delivery_status')
        ->get();
    }

    private function getDailySalesData(Request $request)
    {
        [$startDate, $endDate] = $this->getPeriod($request, 'daily_sales_period', 'month');

        $dailySales = Order::select(
            DB::raw('STR_TO_DATE(date, "%Y-%m-%d") as sale_date'),
            DB::raw('SUM(total_paid) as total_sales'),
            DB::raw('COUNT(*) as order_count')
        )
        ->delivered()
        ->whereBetween(DB::raw('STR_TO_DATE(date, "%Y-%m-%d")'), [$startDate, $endDate])
        ->groupBy('sale_date')
        ->orderBy('sale_date')
        ->get();

        // Format data for Chart.js
        $labels = [];
        $salesData = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $formattedDate = $currentDate->format('Y-m-d');
            $displayDate = $currentDate->format('M d');

            $dayData = $dailySales->first(function ($item) use ($formattedDate) {
                return $item->sale_date == $formattedDate;
            });

            $labels[] = $displayDate;
            $salesData[] = $dayData ? $dayData->total_sales : 0;

            $currentDate->addDay();
        }

        return [
            'labels' => $labels,
            'sales' => $salesData
        ];
    }

    private function getDateRange(Request $request, $start_date_accessor='start_date', $end_date_accessor='end_date', $period_type = 'year'): array
    {
        $now = Carbon::now();
        $startDate = $request->{$start_date_accessor} ? Carbon::parse($request->{$start_date_accessor}) : null;
        $endDate = $request->{$end_date_accessor} ? Carbon::parse($request->{$end_date_accessor}) : null;

        if (!$startDate && !$endDate) {
            return [
                $period_type == 'year' ? $now->copy()->startOfYear() : $now->copy()->startOfMonth(),
                $period_type == 'year' ? $now->copy()->endOfYear() : $now->copy()->endOfMonth(),
            ];
        }

        if (!$startDate) {
            $startDate = $period_type == 'year' ? $now->copy()->startOfYear() : $now->copy()->startOfMonth();
        }
        if (!$endDate) {
            $endDate = $period_type == 'year' ? $now->copy()->endOfYear() : $now->copy()->endOfMonth();
        }

        return [$startDate, $endDate];
    }

    // get only date range
    private function getPeriod(Request $request, $accessor = 'data_period', $period_type = 'year'): array
    {
        // default return current month for month type
        // default return current year for year type
        $now = Carbon::now();
        $startDate = null;
        $endDate = null;
        $period = $request?->{$accessor};
        if ($period) {
            switch ($period) {
                case 'last_year':
                    return [
                        $now->copy()->subYear()->startOfYear(),
                        $now->copy()->subYear()->endOfYear()
                    ];
                case 'this_year':
                    return [
                        $now->copy()->startOfYear(),
                        $now->copy()->endOfYear()
                    ];
                case 'last_month':
                    return [
                        $now->copy()->subMonth()->startOfMonth(),
                        $now->copy()->subMonth()->endOfMonth()
                    ];
                case 'this_month':
                    return [
                        $now->copy()->startOfMonth(),
                        $now->copy()->endOfMonth()
                    ];
                case 'last_3_months':
                    $start = $now->copy()->subMonths(3)->startOfMonth();
                    return [$start, $start->copy()->endOfMonth()];
                case 'last_6_months':
                    $start = $now->copy()->subMonths(6)->startOfMonth();
                    return [$start, $start->copy()->endOfMonth()];
                case 'last_12_months':
                    $start = $now->copy()->subMonths(12)->startOfMonth();
                    return [$start, $start->copy()->endOfMonth()];
                default:
                    return [
                        $period_type == 'year' ? $now->copy()->startOfYear() : $now->copy()->startOfMonth(),
                        $period_type == 'year' ? $now->copy()->endOfYear() : $now->copy()->endOfMonth()
                    ];
            }
        }else{
            if($period_type == 'month'){
                // current month
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
            }else{
                // current year
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
            }
            return [
                $startDate,
                $endDate
            ];
        }
    }
    // ==================== new dashboard ==================== //
}
