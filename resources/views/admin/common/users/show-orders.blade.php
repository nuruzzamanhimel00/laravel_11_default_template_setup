@if($orders->count() > 0)
@foreach ($orders as $order)
    <div class="card mb-2">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Order ID: {{$order->invoice_no }}</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-home fa-2x text-secondary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <address class="mb-0">
                                <strong>{{ $order->customer->full_name }}</strong><br>

                                {{-- Phone --}}
                                @if(!empty($order->shipping_info['phone']))
                                {{ $order->shipping_info['phone'] ?? $order->customer->phone }}<br>
                                @else
                                {{$order->customer->phone }} <br>
                                @endif

                                {{-- Address --}}
                                @if(!empty($order->shipping_info['address']))
                                    {{ $order->shipping_info['address'] }}<br>
                                @endif

                                {{-- Total --}}
                                <b>Total:</b> {{ addCurrency($order->total) }}<br>

                                {{-- Platform --}}
                                <b>Platform:</b> {!! $order->platform_html !!}<br>
                            </address>

                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <strong>Date: </strong>
                        <span class="text-muted">{{$order->date }}</span>
                    </p>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center border-top border-bottom py-3">
                <div>
                    <strong>Amount Payment: </strong>
                </div>
                <div>
                    <span class="fw-bold">{{addCurrency($order->total_paid)}} </span>
                    <span class="">{!! $order->payment_status_html !!}</span>
                </div>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2 mb-2 mb-md-0">
                    <div class="d-flex">
                        <span class="me-2">Order Status:</span>
                        <span class="">{!! $order->order_status_html !!}</span>
                    </div>
                    <div class="d-flex">
                        <span class="me-2">Delivery Status:</span>
                        <span class="">{!! !empty($order->delivery_status) ? $order->delivery_status_html  : 'N/A' !!}</span>
                    </div>
                </div>
                <div class="btn-group gap-2" role="group">
                    @if (auth()->user()->can('Make Payment Order') && $order->total > $order->total_paid && $order->status != \App\Models\Order::STATUS_CANCEL)
                    <a href="{{route('order.payment', $order->id) }}" class="btn btn-outline-primary btn-sm">Make Payment</a>
                    @endif
                    @if (auth()->user()->can('View Payment Order') )
                    <button type="button" class="btn btn-outline-success btn-sm list_payment_btn" data-id="{{$order->id}}">View Payment</button>
                    @endif
                    @if (auth()->user()->can('Show Order'))
                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-dark btn-sm">View Details</a>
                    @endif
                    @if (auth()->user()->can('Histories Order'))
                    <a href="{{ route('order.histories', $order->id) }}" class="btn btn-outline-dark btn-sm">Order Histories</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach

@endif
{!! $orders->links('vendor.pagination.bootstrap-5') !!}
