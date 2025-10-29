@extends('admin.layouts.app')

@section('content')
    <div x-data="analyticsCharts()" x-init="initialize()">

        <div class="chart-container">
            <h3>User Creation Trend</h3>
            <template x-if="loading">
                <div class="chart-loading">Loading chart data...</div>
            </template>
            <template x-if="errors.lineChart">
                <div class="chart-error" x-text="errors.lineChart"></div>
            </template>
            <canvas x-ref="lineChart"></canvas>
        </div>

        <div class="chart-container">
            <h3>Patient Creation Monthly</h3>
            <template x-if="loading">
                <div class="chart-loading">Loading chart data...</div>
            </template>
            <template x-if="errors.barChart">
                <div class="chart-error" x-text="errors.barChart"></div>
            </template>
            <canvas x-ref="barChart"></canvas>
        </div>


        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stat ic-bg-dashboard-card text-white h-90">
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="float-start mini-stat-img me-2">
                                <i class="fa fa-users bx-fade-right fa-2x pt-3"></i>
                            </div>
                            <h5 class="font-size-16 text-uppercase text-white-50">{{ __('Total Customer') }}</h5>
                            <h4 class="fw-medium font-size-24">{{ $total_customers }}
                            </h4>
                        </div>
                        <div class="pt-2">
                            <div class="float-end">
                                <a href="{{ route('users.index') }}" class="text-white-50"><i
                                        class="mdi mdi-arrow-right h5"></i></a>
                            </div>

                            <p class="text-white-50 mb-0 mt-1">{{ __('Total Home Customers') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stat bg-blue-grey text-white h-90">
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="float-start mini-stat-img me-2">
                                <i class="fa fa-list bx-fade-right fa-2x pt-3"></i>
                                {{--                            <i class="mdi mdi-cube-outline bx-fade-right"></i> --}}
                                {{--                            <img src="assets/images/services-icon/01.png" alt=""> --}}
                            </div>
                            <h5 class="font-size-16 text-uppercase text-white-50">{{ __('Total Restaurants') }}</h5>
                            <h4 class="fw-medium font-size-24">{{ $total_restaurants }}
                                {{--                            <i class="mdi mdi-arrow-up text-success ms-2"></i> --}}
                            </h4>
                            {{--                        <div class="mini-stat-label bg-success"> --}}
                            {{--                            <p class="mb-0">+ 12%</p> --}}
                            {{--                        </div> --}}
                        </div>
                        <div class="pt-2">
                            <div class="float-end">
                                <a href="{{ route('restaurants.index') }}" class="text-white-50"><i
                                        class="mdi mdi-arrow-right h5"></i></a>
                            </div>

                            <p class="text-white-50 mb-0 mt-1">{{ __('Restaurants In Application') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stat bg-primary text-white card_3 h-90">
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="float-start mini-stat-img me-2">
                                <i class="far fa-user-circle bx-fade-right fa-2x pt-3"></i>
                            </div>
                            <h5 class="font-size-16 text-uppercase text-white-50">Total Suppliers</h5>
                            <h4 class="fw-medium font-size-24">{{ $total_suppliers }}
                                {{-- <i class="mdi mdi-arrow-up text-success ms-2"></i> --}}
                            </h4>
                            {{-- <div class="mini-stat-label bg-info">
                            <p class="mb-0"> 00%</p>
                        </div> --}}
                        </div>
                        <div class="pt-2">
                            <div class="float-end">
                                <a href="{{ route('suppliers.index') }}" class="text-white-50"><i
                                        class="mdi mdi-arrow-right h5"></i></a>
                            </div>

                            <p class="text-white-50 mb-0 mt-1">{{ __('Suppliers In Application') }} </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stat bg-info text-white card_4 h-90">
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="float-start mini-stat-img me-2">
                                <span class="fa-2x fw-bold">৳</span>
                                {{-- <i class="fa fa-dollar-sign bx-fade-right fa-2x pt-3"></i> --}}
                            </div>
                            <h5 class="font-size-16 text-uppercase text-white-50">Product Sold</h5>
                            <h4 class="fw-medium font-size-24">{{ $last_month_sales }} <i
                                    class="mdi mdi-arrow-up text-success ms-2"></i></h4>
                            {{-- <div class="mini-stat-label bg-warning">
                            <p class="mb-0">+ 84%</p>
                        </div> --}}
                        </div>
                        <div class="pt-4">
                            <div class="float-end">
                                <a href="#" class="text-white-50"><i class="mdi mdi-arrow-right h5"></i></a>
                            </div>

                            <p class="text-white-50 mb-0 mt-1">Since last month</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-body p-3">
                        <h5 class="fw-bold mb-3">Sales Dashboard</h5>
                        <form id="dateRangeForm" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ $dateRange['start'] }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ $dateRange['end'] }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-2"></i> Apply Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card mb-4">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Sales</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        ${{ number_format($orderStats['total_sales'], 2) }}
                                    </h5>
                                    <p class="mb-0 text-sm">
                                        <span class="text-success text-sm font-weight-bolder">
                                            <i class="fas fa-chart-line"></i>
                                        </span>
                                        For selected period
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="stat-icon bg-gradient-primary text-white">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card mb-4">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Orders</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ number_format($orderStats['total_orders']) }}
                                    </h5>
                                    <p class="mb-0 text-sm">
                                        <span class="text-success text-sm font-weight-bolder">
                                            <i class="fas fa-shopping-cart"></i>
                                        </span>
                                        For selected period
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="stat-icon bg-gradient-info text-white">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card mb-4">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Average Order</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        ${{ number_format($orderStats['average_order_value'], 2) }}
                                    </h5>
                                    <p class="mb-0 text-sm">
                                        <span class="text-success text-sm font-weight-bolder">
                                            <i class="fas fa-calculator"></i>
                                        </span>
                                        Per order
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="stat-icon bg-gradient-success text-white">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card mb-4">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Items Sold</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ number_format($orderStats['total_items']) }}
                                    </h5>
                                    <p class="mb-0 text-sm">
                                        <span class="text-success text-sm font-weight-bolder">
                                            <i class="fas fa-boxes"></i>
                                        </span>
                                        Total quantity
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="stat-icon bg-gradient-warning text-white">
                                    <i class="fas fa-box"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Monthly Sales Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header p-3">
                        <h6 class="mb-0">Monthly Sales Performance</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart-container">
                            <canvas id="monthlySalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Daily Sales Chart and Payment Status -->
        <div class="row mb-4">
            <div class="col-xl-8 col-lg-7">
                <div class="card dashboard-card">
                    <div class="card-header p-3">
                        <h6 class="mb-0">Daily Sales Trend</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart-container">
                            <canvas id="dailySalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5">
                <div class="card dashboard-card">
                    <div class="card-header p-3">
                        <h6 class="mb-0">Payment Status</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="paymentStatusChart"></canvas>
                        </div>
                        <div class="mt-4">
                            @foreach ($paymentStatusData as $status)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span
                                            class="status-badge bg-{{ getPaymentStatusColor($status->payment_status) }}">
                                            {{ ucfirst($status->payment_status ?? 'Unknown') }}
                                        </span>
                                    </div>
                                    <div>
                                        <span
                                            class="text-sm font-weight-bold">${{ number_format($status->total_amount, 2) }}</span>
                                        <span class="text-muted text-sm">({{ $status->count }} orders)</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Status and Top Products -->
        <div class="row">
            <div class="col-xl-4 col-lg-5">
                <div class="card dashboard-card mb-4">
                    <div class="card-header p-3">
                        <h6 class="mb-0">Delivery Status</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="deliveryStatusChart"></canvas>
                        </div>
                        <div class="mt-4">
                            @foreach ($deliveryStatusData as $status)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span
                                            class="status-badge bg-{{ getDeliveryStatusColor($status->delivery_status) }}">
                                            {{ ucfirst($status->delivery_status ?? 'Unknown') }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-sm font-weight-bold">{{ $status->count }} orders</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-7">
                <div class="card dashboard-card mb-4">
                    <div class="card-header p-3">
                        <h6 class="mb-0">Top Selling Products</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Product</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Quantity</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Revenue</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Avg. Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topProducts as $product)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-2">
                                                    <div>
                                                        <img src="{{ asset('img/product-placeholder.png') }}"
                                                            class="avatar me-3">
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $product->product_name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">ID:
                                                            #{{ $product->product_id }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">
                                                    {{ number_format($product->total_quantity) }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">
                                                    ${{ number_format($product->total_revenue, 2) }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">
                                                    ${{ number_format($product->total_revenue / $product->total_quantity, 2) }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title mb-4">User Analysis</h4>

                        <div class="row justify-content-center">
                            <div class="col-sm-4">
                                <div class="text-center">
                                    <h5 class="mb-0 font-size-20">{{ $user_this_month }}</h5>
                                    <p class="text-muted">This Month</p>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-center">
                                    <h5 class="mb-0 font-size-20"> {{ $user_last_six_month }}</h5>
                                    <p class="text-muted">Last 6 Month</p>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-center">
                                    <h5 class="mb-0 font-size-20">{{ $user_this_year }}</h5>
                                    <p class="text-muted">This Year</p>
                                </div>
                            </div>
                        </div>

                        <canvas id="lineChart" height="300"></canvas>

                    </div>
                </div>
            </div> <!-- end col -->

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title mb-4">Analysis</h4>

                        <div class="row justify-content-center">
                            <div class="col-sm-4">
                                <div class="text-center">
                                    <h5 class="mb-0 font-size-20">$ 1111</h5>
                                    <p class="text-muted">This Month</p>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-center">
                                    <h5 class="mb-0 font-size-20"> $ 6666</h5>
                                    <p class="text-muted">Last 6 Month</p>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-center">
                                    <h5 class="mb-0 font-size-20">$ 9999</h5>
                                    <p class="text-muted">This Year</p>
                                </div>
                            </div>
                        </div>

                        <canvas id="bar" height="300"></canvas>

                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div>
@endsection
@push('style')
    <style>
        .dashboard-card {
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            font-size: 2rem;
            width: 4rem;
            height: 4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .date-filter {
            background-color: #fff;
            border-radius: 0.5rem;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Responsive chart containers */
        .chart-container {
            position: relative;
            margin-bottom: 2rem;
            height: 300px;
            width: 100%;
        }

        /* Loading state */
        .chart-loading {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 10;
        }

        /* Error state */
        .chart-error {
            padding: 1rem;
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }
    </style>
@endpush
@push('script')
    <!-- Alpine.js (production minified version with defer) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <!-- Chart JS -->
    <script src="{{ asset('libs/chart.js/Chart.bundle.min.js') }}"></script>
    {{--    <script src="{{ asset('js/pages/chartjs.init.js')}}"></script> --}}
    <script>
        // ========== Alpine ================>
        // Debounce function to limit resize operations
        function debounce(func, wait) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }
        document.addEventListener('alpine:init', () => {
            Alpine.data('analyticsCharts', () => ({
                charts: {
                    lineChart: null,
                    barChart: null
                },
                loading: true,
                errors: {
                    lineChart: null,
                    barChart: null
                },
                chartData: {
                    lineData: null,
                    barData: null
                },
                resizeObserver: null,
                initialize() {
                    // Load chart data (simulated here, replace with your data source)
                    this.fetchChartData()
                        .then(() => {
                            this.loading = false;
                            this.initializeCharts();
                            this.setupResizeHandling();
                        })
                        .catch(error => {
                            this.loading = false;
                            console.error('Error loading chart data:', error);
                            this.errors.lineChart =
                                'Failed to load chart data. Please try again later.';
                            this.errors.barChart =
                                'Failed to load chart data. Please try again later.';
                        });

                    // Clean up on element removal
                    this.$el._x_cleanups = this.$el._x_cleanups || [];
                    this.$el._x_cleanups.push(() => this.cleanup());
                },

                // Fetch data from server or use embedded data
                async fetchChartData() {
                    // In a real app, fetch from API or get from PHP rendered variable
                    // For now, using sample data

                    const months = ["January", "February", "March", "April", "May", "June", "July",
                        "August",
                        "September", "October", "November", "December"
                    ];

                    // Replace with actual data or PHP rendering
                    const userCreationData = [65, 59, 80, 81, 56, 55, 40, 65, 59, 80, 81, 56];
                    const patientCreationData = [6500, 5900, 8000, 8100, 5600, 5500, 4000, 6500,
                        5900, 8000, 8100, 5600
                    ];

                    // You would normally do this server-side:
                    // const userCreationData = JSON.parse('<?php echo json_encode($dailySales); ?>');
                    // const patientCreationData = JSON.parse('<?php echo json_encode($dailySales); ?>');

                    this.chartData.lineData = {
                        labels: months,
                        datasets: [{
                            label: "User Created",
                            fill: true,
                            tension: 0.4, // Updated property name from lineTension
                            backgroundColor: "rgba(60, 76, 207, 0.2)",
                            borderColor: "#3c4ccf",
                            borderCapStyle: "butt",
                            borderDash: [],
                            borderDashOffset: 0,
                            borderJoinStyle: "miter",
                            pointBorderColor: "#3c4ccf",
                            pointBackgroundColor: "#fff",
                            pointBorderWidth: 1,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: "#3c4ccf",
                            pointHoverBorderColor: "#fff",
                            pointHoverBorderWidth: 2,
                            pointRadius: 2,
                            pointStyle: 'circle',
                            pointHitRadius: 10,
                            data: userCreationData
                        }]
                    };

                    this.chartData.barData = {
                        labels: months,
                        datasets: [{
                            label: "Patient Created",
                            backgroundColor: "#02a499",
                            borderColor: "#02a499",
                            borderWidth: 1,
                            hoverBackgroundColor: "#007a72",
                            hoverBorderColor: "#016b65",
                            data: patientCreationData
                        }]
                    };
                },

                initializeCharts() {
                    try {
                        // Configure Chart.js defaults
                        Chart.defaults.color = "#adb5bd";
                        Chart.defaults.borderColor = "rgba(108, 120, 151, 0.1)";

                        // Initialize line chart
                        this.initLineChart();

                        // Initialize bar chart
                        this.initBarChart();
                    } catch (error) {
                        console.error('Error initializing charts:', error);
                        this.errors.lineChart =
                            'Failed to initialize charts. Please check browser console for details.';
                        this.errors.barChart =
                            'Failed to initialize charts. Please check browser console for details.';
                    }
                },

                setupResizeHandling() {
                    // Use ResizeObserver for more efficient resize handling
                    if (window.ResizeObserver) {
                        this.resizeObserver = new ResizeObserver(debounce(() => {
                            this.refreshCharts();
                        }, 250));

                        this.resizeObserver.observe(this.$refs.lineChart.parentNode);
                        this.resizeObserver.observe(this.$refs.barChart.parentNode);
                    } else {
                        // Fallback to window resize event
                        window.addEventListener('resize', debounce(() => {
                            this.refreshCharts();
                        }, 250));
                    }
                },

                refreshCharts() {
                    // Destroy and recreate charts for proper resizing
                    this.destroyCharts();
                    this.initializeCharts();
                },

                destroyCharts() {
                    // Safely destroy existing chart instances
                    Object.values(this.charts).forEach(chart => {
                        if (chart instanceof Chart) {
                            chart.destroy();
                        }
                    });
                },

                initLineChart() {
                    if (!this.$refs.lineChart || this.errors.lineChart) return;

                    const ctx = this.$refs.lineChart.getContext('2d');
                    this.charts.lineChart = new Chart(ctx, {
                        type: 'line',
                        data: this.chartData.lineData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                }
                            },
                            interaction: {
                                mode: 'nearest',
                                intersect: true
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                },

                initBarChart() {
                    if (!this.$refs.barChart || this.errors.barChart) return;

                    const ctx = this.$refs.barChart.getContext('2d');
                    this.charts.barChart = new Chart(ctx, {
                        type: 'bar',
                        data: this.chartData.barData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                },

                cleanup() {
                    // Clean up event listeners and observers
                    this.destroyCharts();

                    if (this.resizeObserver) {
                        this.resizeObserver.disconnect();
                    }
                }
            }))
        })

        // ========== Alpine ================>
    </script>
@endpush
