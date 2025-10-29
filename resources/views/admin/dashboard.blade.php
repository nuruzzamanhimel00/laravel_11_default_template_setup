@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card mini-stat ic-bg-dashboard-card text-white h-90">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-start mini-stat-img me-2">
                            <i class="fa fa-users bx-fade-right fa-2x pt-3"></i>
                        </div>
                        <h5 class="font-size-16 text-uppercase text-white-50">{{ __('Total Customer') }}</h5>
                        <h4 class="fw-medium font-size-24">0
                        </h4>
                    </div>
                    <div class="pt-2">
                        <div class="float-end">
                            <a href="#" class="text-white-50"><i
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
                        <h4 class="fw-medium font-size-24">0
                        </h4>
                    </div>
                    <div class="pt-2">
                        <div class="float-end">
                            <a href="#" class="text-white-50"><i
                                    class="mdi mdi-arrow-right h5"></i></a>
                        </div>

                        <p class="text-white-50 mb-0 mt-1">{{ __('Restaurants In Application') }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>



</div>

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

@endpush
