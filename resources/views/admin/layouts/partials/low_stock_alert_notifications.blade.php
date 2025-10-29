<div  class="dropdown d-inline-block" >
    @inject('productService', 'App\Services\Product\ProductService')
    @php
    $getLowStockProducts = $productService->getLowStockProducts();

    @endphp

    <button type="button" class="btn header-item noti-icon"
            id="page-header-notifications-dropdown"
            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="mdi mdi-alert"></i>
        @if($getLowStockProducts->count() > 0)
        <span class="badge bg-danger py-1 px-2 rounded-pill" >{{$getLowStockProducts->count()}}</span>
        @endif
    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown"
        >
        <div class="p-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="m-0 font-size-16">Low Stock Alert Notifications ({{$getLowStockProducts->count()}}) </h5>
                </div>
            </div>
        </div>
        <div style="max-height: 230px;overflow-y: scroll;">


                @foreach($getLowStockProducts as $product)
                <a href="{{route('product.low.stock')}}?product_id={{$product->id}}" class="text-reset notification-item">
                    <div class="d-flex align-items-center unread">
                        <!-- Left side: Product image -->
                        <div class="me-3">
                            <img src="{{ $product->image_url ?? asset('images/default-product.png') }}" alt="Product"
                                class="rounded-circle" width="48" height="48" style="object-fit: cover;">
                        </div>

                        <!-- Middle: Product details -->
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $product->name }}</h6>
                            <small class="text-muted d-block">Barcode: {{ $product->barcode }}</small>
                            <small class="text-muted d-block">Category: {{ $product->category->name ?? 'N/A' }}</small>
                        </div>

                        <!-- Right side: Alert quantity -->
                        <div class="text-end">
                            <span class="badge bg-danger rounded-pill">
                                {{ $product->total_stock_quantity }}
                            </span>
                        </div>
                    </div>
                </a>
                @endforeach



        </div>
        <a href="{{route('product.low.stock')}}"  class="text-center notify-all text-muted d-block w-100 notification-all-item py-3">
            View all <i class="fi-arrow-right"></i>
        </a>

    </div>
</div>
@push('style')


@endpush
@push('script')
<style>
.notification-item .d-flex {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f1f1;
}
.notification-item img {
    border: 2px solid #dee2e6;
}
.unread {
    background-color: #f8f9fa;
}
</style>

@endpush
