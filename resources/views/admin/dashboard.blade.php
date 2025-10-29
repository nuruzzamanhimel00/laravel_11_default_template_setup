@extends('admin.layouts.app')

@section('content')
<div x-data="analyticsCharts()" x-init="initializeCharts()">

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card mini-stat ic-bg-dashboard-card text-white h-90">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-start mini-stat-img me-2">
                            <i class="fa fa-users bx-fade-right fa-2x pt-3"></i>
                        </div>
                        <h5 class="font-size-16 text-uppercase text-white-50">{{ __('Total Customer') }}</h5>
                        <h4 class="fw-medium font-size-24">{{ readableNumberFormat($total_customers) }}
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
            <div class="card dashboard-card mini-stat bg-blue-grey text-white h-90">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-start mini-stat-img me-2">
                            <i class="fa fa-list bx-fade-right fa-2x pt-3"></i>
                        </div>
                        <h5 class="font-size-16 text-uppercase text-white-50">{{ __('Total Restaurants') }}</h5>
                        <h4 class="fw-medium font-size-24">{{ readableNumberFormat($total_restaurants) }}
                        </h4>
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
            <div class="card dashboard-card mini-stat bg-primary text-white card_3 h-90">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-start mini-stat-img me-2">
                            <i class="far fa-user-circle bx-fade-right fa-2x pt-3"></i>
                        </div>
                        <h5 class="font-size-16 text-uppercase text-white-50">Total Suppliers</h5>
                        <h4 class="fw-medium font-size-24">{{ readableNumberFormat($total_suppliers) }}
                        </h4>
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
            <div class="card dashboard-card mini-stat bg-info text-white card_4 h-90">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-start mini-stat-img me-2">
                            <span class="fa-2x fw-bold">৳</span>
                        </div>
                        <h5 class="font-size-16 text-uppercase text-white-50">Sold Last month</h5>
                        <h4 class="fw-medium font-size-24">{{ readableNumberFormat($last_month_sales) }}
                            {{-- <i class="mdi mdi-arrow-up text-success ms-2"></i> --}}
                        </h4>
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

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 ">
            <div class="card dashboard-card mb-4 dashboard-card mini-stat h-90 pe-3 ic_green">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Sales</p>
                                <h5 class="font-weight-bolder mb-3">
                                    {{ addCurrency($orderStats['total_sales']) }}
                                </h5>
                                <p class="mb-0 text-sm">
                                    <span class="text-success text-sm font-weight-bolder">
                                        <i class="fas fa-chart-line"></i>
                                    </span>
                                    For this month
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="stat-icon bg-gradient-primary text-white">
                                ৳
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card mb-4 dashboard-card mini-stat h-90 pe-3 ic_blue">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Orders</p>
                                <h5 class="font-weight-bolder mb-3">
                                    {{ $orderStats['total_orders'] }}
                                </h5>
                                <p class="mb-0 text-sm">
                                    <span class="text-success text-sm font-weight-bolder">
                                        <i class="fas fa-shopping-cart"></i>
                                    </span>
                                    For this month
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
            <div class="card dashboard-card mb-4 dashboard-card mini-stat h-90 pe-3 ic_black">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Average Order</p>
                                <h5 class="font-weight-bolder mb-3">
                                    {{ addCurrency($orderStats['average_order_value']) }}
                                </h5>
                                <p class="mb-0 text-sm">
                                    <span class="text-success text-sm font-weight-bolder">
                                        <i class="fas fa-calculator"></i>
                                    </span>
                                    Per order this month
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
            <div class="card dashboard-card mb-4 dashboard-card mini-stat h-90 pe-3 ic_purple">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Items Sold</p>
                                <h5 class="font-weight-bolder mb-3">
                                    {{ $orderStats['total_items'] }}
                                </h5>
                                <p class="mb-0 text-sm">
                                    <span class="text-success text-sm font-weight-bolder">
                                        <i class="fas fa-boxes"></i>
                                    </span>
                                    Total quantity this month
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
            <div class="card">
                <div class="card-header p-3">
                    <h6 class="mb-0">Monthly Sales Performance</h6>
                    {{-- <div class="row">
                            <div class="col-md-3">
                                <h6 class="mb-0">Monthly Sales Performance</h6>
                            </div>
                            <div class="col-md-5">
                                <div class="align-middle">
                                    <input x-ref="monthlySalesPicker" type="text" placeholder="Y-m-d" class="form-control" />
                                    <button class="btn btn-primary" @click="toggleDatePicker">Apply</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="filter-monthly-data">Filter</label>
                                <select class="form-select" id="filter-monthly-data" x-model="chartData.monthlySalesChart.filter_by" @change="filterMonthlyData">
                                        <option value="this_month">Current month</option>
                                        <option value="last_month">Previous month</option>
                                </select>
                            </div>
                        </div> --}}
                </div>
                <div class="card-body p-3">
                    <div class="chart-container">
                        <canvas x-ref="monthlySalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Daily Sales Chart and Payment Status -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card dashboard-card h-100">
                <div class="card-header p-3">
                    <h6 class="mb-0">Daily Sales Trend</h6>
                </div>
                <div class="card-body p-3">
                    <div class="chart-container">
                        <canvas x-ref="dailySalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card dashboard-card h-100">
                <div class="card-header p-3">
                    <h6 class="mb-0">Payment Status</h6>
                </div>
                <div class="card-body p-3">
                    <div class="chart-container" style="height: 250px;">
                        <canvas x-ref="paymentStatusChart"></canvas>
                    </div>
                    <div class="mt-4">
                        @foreach ($paymentStatusData as $status)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="badge-{{ getPaymentStatusColor($status->payment_status) }}">
                                    {{ $status->payment_status ? ucfirst(str_replace('_', ' ', $status->payment_status)) : 'Unknown' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm font-weight-bold">{{ addCurrency($status->total_amount) }}</span>
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
            <div class="card dashboard-card mb-4 h-100">
                <div class="card-header p-3">
                    <h6 class="mb-0">Delivery Status</h6>
                </div>
                <div class="card-body p-3">
                    <div class="chart-container" style="height: 250px;">
                        <canvas x-ref="deliveryStatusChart"></canvas>
                    </div>
                    <div class="mt-4">
                        @foreach ($deliveryStatusData as $status)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="badge-{{ getDeliveryStatusColor($status->delivery_status) }}">
                                    {{ $status->delivery_status ? ucfirst(str_replace('_', ' ', $status->delivery_status)) : 'Unknown' }}
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
            <div class="card dashboard-card mb-4 h-100">
                <div class="card-header p-3">
                    <h6 class="mb-0">Top Selling Products (Last 12 month)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-black text-xxs font-weight-bolder opacity-7">
                                        Product</th>
                                    <th class="text-uppercase text-black text-xxs font-weight-bolder opacity-7 ps-2">
                                        Quantity</th>
                                    <th class="text-uppercase text-black text-xxs font-weight-bolder opacity-7 ps-2">
                                        Item Total</th>
                                    <th class="text-uppercase text-black text-xxs font-weight-bolder opacity-7 ps-2">
                                        Avg. Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topProducts as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex">
                                            <div>
                                                <img src="{{ getStorageImage($product->image) }}" class="avatar me-3"
                                                    width="40" height="40">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $product->product_name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">
                                            {{ $product->total_quantity }}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">
                                            {{ addCurrency($product->item_total) }}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">
                                            {{ addCurrency(currencyFormatter($product->item_total / $product->total_quantity)) }}
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
</div>
@php
$paymentStatusLabels = $paymentStatusData->pluck('payment_status')->map(function ($status) {
return $status ? ucfirst(str_replace('_', ' ', $status)) : 'Unknown';
});
$paymentStatusCounts = $paymentStatusData->pluck('count');
$deliveryStatusLabels = $deliveryStatusData->pluck('delivery_status')->map(function ($status) {
return $status ? ucfirst(str_replace('_', ' ', $status)) : 'Unknown';
});
$deliveryStatusCounts = $deliveryStatusData->pluck('count');
@endphp
@endsection
@push('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
    /* overflow: hidden; */
}

.status-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 2rem;
    font-size: 0.85rem;
    font-weight: 500;
}

.chart-container {
    position: relative;
    margin-bottom: 2rem;
    height: 300px;
    width: 100%;
}

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

.chart-error {
    padding: 1rem;
    color: #721c24;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 0.25rem;
    margin-bottom: 1rem;
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #6a82fb, #fc5c7d) !important;
}

.bg-gradient-info {
    background: linear-gradient(45deg, #00c6ff, #0072ff) !important;
}

.bg-gradient-success {
    background: linear-gradient(45deg, #00b09b, #96c93d) !important;
}

.bg-gradient-warning {
    background: linear-gradient(45deg, #ff758c, #ff7eb3) !important;
}
</style>
@endpush
@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('analyticsCharts', () => ({
        defaultChartOptions: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        },
        datePickerConfig: {
            mode: "range",
            altInput: true,
            altFormat: 'Y-m-d',
            defaultDate: ["2025-04-15", "2025-04-23"],
            dateFormat: 'Y-m-d',
            maxDate: new Date().toISOString().split('T')[0],
        },
        paymentStatusLabels: @json($paymentStatusLabels),
        paymentStatusCounts: @json($paymentStatusCounts),
        deliveryStatusLabels: @json($deliveryStatusLabels),
        deliveryStatusCounts: @json($deliveryStatusCounts),
        charts: {},
        loading: true,
        error: null,
        chartData: {
            monthlySalesChart: {
                filter: {
                    filter_by: 'this_month',
                    filterOptions: {
                        this_month: {
                            title: 'Current month',
                            value: 'this_month'
                        },
                        last_month: {
                            title: 'Previous month',
                            value: 'last_month'
                        },
                    },
                    isOpen: false,
                    datePicRef: 'monthlySalesPicker',
                    startDate: null,
                    endDate: null,
                },
                ref: 'monthlySalesChart',
                chart: {
                    type: 'bar',
                    data: {
                        labels: @json($monthlySales['labels']),
                        datasets: [{
                            label: 'Sales (৳)',
                            data: @json($monthlySales['sales']),
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            yAxisID: 'y' // Left axis for sales
                        }, {
                            label: 'Orders',
                            data: {!!json_encode($monthlySales['orders']) !!},
                            type: 'line',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 3,
                            pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                            pointRadius: 5,
                            tension: 0.4,
                            yAxisID: 'y1'
                        }]
                    },
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
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.datasetIndex === 0) {
                                            label += new Intl.NumberFormat('en-US', {
                                                style: 'currency',
                                                currency: 'BDT'
                                            }).format(context.raw);
                                        } else {
                                            label += context.raw;
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Sales (৳)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '৳' + value.toLocaleString();
                                    }
                                }
                            },
                            y1: {
                                type: 'linear',
                                beginAtZero: true,
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Orders'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                },
                                // suggestedMax: Math.max(...@json($monthlySales['orders'])) * 1.2
                            }
                        }
                    }
                },
            },
            paymentStatusChart: {
                ref: 'paymentStatusChart',
                chart: {
                    type: 'doughnut',
                    data: {
                        labels: @json($paymentStatusLabels),
                        datasets: [{
                            label: 'Payment Status',
                            data: @json($paymentStatusCounts),
                            backgroundColor: [
                                'rgba(40, 167, 69, 0.8)',
                                'rgba(255, 193, 7, 0.8)',
                                'rgba(220, 53, 69, 0.8)',
                                'rgba(23, 162, 184, 0.8)',
                                'rgba(108, 117, 125, 0.8)'
                            ],
                            // backgroundColor: [
                            //     'rgba(40, 167, 69, 0.8)',
                            //     'rgba(255, 193, 7, 0.8)',
                            //     'rgba(220, 53, 69, 0.8)',
                            //     'rgba(23, 162, 184, 0.8)',
                            //     'rgba(108, 117, 125, 0.8)'
                            // ],
                            borderColor: 'white',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) =>
                                            a + b, 0);
                                        const percentage = Math.round((value / total) *
                                            100);
                                        return `${label}: ${value} orders (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                },
            },
            deliveryStatusChart: {
                ref: 'deliveryStatusChart',
                chart: {
                    type: 'doughnut',
                    data: {
                        labels: @json($deliveryStatusLabels),
                        datasets: [{
                            label: 'Delivery Status',
                            data: @json($deliveryStatusCounts),
                            backgroundColor: [
                                'rgba(190, 27, 220, 0.8)', // beguni
                                'rgba(40, 167, 69, 0.8)',
                                'rgb(0, 171, 150)', //info
                                'rgb(11, 222, 0)', // light green
                                'rgba(148, 117, 2, 0.86)', // koire
                            ],
                            borderColor: 'white',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) =>
                                            a + b, 0);
                                        const percentage = Math.round((value / total) *
                                            100);
                                        return `${label}: ${value} orders (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                },
            },
            dailySalesChart: {
                ref: 'dailySalesChart',
                chart: {
                    type: 'bar',
                    data: {
                        labels: @json($dailySales['labels']),
                        datasets: [{
                            label: 'Daily Sales (৳)',
                            data: @json($dailySales['sales']),
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                            pointRadius: 3,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                // mode: 'index',
                                // intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.datasetIndex === 0) {
                                            label += new Intl.NumberFormat('en-US', {
                                                style: 'currency',
                                                currency: 'USD'
                                            }).format(context.raw);
                                        } else {
                                            label += context.raw;
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '৳' + value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                },
            }
        },

        async initializeCharts() {
            try {
                this.loading = true;
                this.error = null;
                this.initAllCharts();
                // this.initAllDateRangePicker();
                this.setupResizeHandler();
                // flatpickr(this.$refs.dateInput, {
                //     mode: "range",
                //     altInput: true,
                //     altFormat: 'Y-m-d',
                //     dateFormat: 'Y-m-d',
                //     maxDate: new Date().toISOString().split('T')[0], // Disable future dates
                //     onClose: (selectedDates) => {
                //         console.log('date', selectedDates)
                //     }
                // })
            } catch (err) {
                console.error('Failed to initialize charts:', err);
                this.error = 'Failed to initialize charts. Please try again later.';
            } finally {
                this.loading = false;
            }
        },

        initAllCharts() {
            const allCharts = Object.keys(this.chartData);
            allCharts.forEach(chart => {
                if (this?.chartData[chart]?.ref && this?.chartData[chart]?.chart && this
                    ?.chartData[chart]?.chart.data.labels.length > 0) {
                    const chartRef = this.$refs[this.chartData[chart].ref];
                    const ctx = chartRef.getContext('2d');
                    this.charts[chart] = new Chart(ctx, this.chartData[chart].chart);
                }
            });
        },
        initAllDateRangePicker() {
            const rangePickers = Object.keys(this.chartData);
            const dataConfig = {
                ...this.datePickerConfig
            };
            rangePickers.forEach(chart => {
                const filter = this.chartData[chart].filter;
                if (filter && filter?.datePicRef) {
                    const ref = filter.datePicRef;
                    let config = {};
                    if (chart == 'monthlySalesChart') {
                        config = {
                            onClose: this.handleMonthlySalesDateChange
                        }
                        config = {
                            ...config,
                            ...dataConfig
                        };
                    }
                    flatpickr(this.$refs[ref], {
                        ...config
                    });
                }
            });
        },
        setupResizeHandler() {
            const resizeHandler = () => {
                Object.values(this.charts).forEach(chart => {
                    if (chart && typeof chart.resize === 'function') {
                        chart.resize();
                    }
                });
            };

            const debouncedResize = this.debounce(resizeHandler, 250);
            window.addEventListener('resize', debouncedResize);

            // Cleanup on component destroy
            this.$cleanup = () => {
                window.removeEventListener('resize', debouncedResize);
                this.destroyCharts();
            };
        },

        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        destroyCharts() {
            Object.values(this.charts).forEach(chart => {
                if (chart && typeof chart.destroy === 'function') {
                    chart.destroy();
                }
            });
            this.charts = {};
        },

        // filters
        handleMonthlySalesDateChange(dateRange) {
            if (dateRange && dateRange.length > 0) {
                this.chartData.monthlySalesChart.filter.startDate = dateRange[0];
                this.chartData.monthlySalesChart.filter.endDate = dateRange[1];
            }
        },
        filterMonthlyData() {
            const sales_period = this.chartData?.monthlySalesChart?.filter?.filter_by ?? '';
            console.log('sales_period:', sales_period)
        }
        // filters
    }));
});
</script>
@endpush
