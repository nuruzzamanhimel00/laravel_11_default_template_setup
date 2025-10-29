@extends('admin.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7 d-print-none">
                            {{-- <h3 class="card-title">Order Items</h3> --}}
                            @php
                            $isOutOfStock = false;
                            @endphp

                            @if($sale->order_items->isNotEmpty() && $sale->order_status == \App\Models\Order::STATUS_PENDING)
                                <ul>
                                    @foreach ($sale->order_items as $order_item)
                                        @if($order_item->product->total_stock_quantity < $order_item->quantity)
                                            @php $isOutOfStock = true; @endphp
                                            <li>
                                                {{ ucwords($order_item->product->name) }}
                                                <span class="text-danger">
                                                    [Stock: {{ number_format($order_item->quantity) }}/{{ number_format($order_item->product->total_stock_quantity) }}]
                                                </span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                            @if($isOutOfStock)
                            <div class="alert alert-danger">
                                <i class="fa fa-exclamation-circle"></i> Out of stock items detected. Please update inventory before proceeding to the next status.
                            </div>
                            @endif
                            @if(!$isOutOfStock)
                            <form action="{{route('order.status.update', $sale->id)}}" method="post" id="form" data-parsley-validate>
                                @csrf
                                <div class="mb-3">
                                    <label for="order_status" class="form-label">Status</label>
                                    <select class="form-select" id="order_status" name="status" required>
                                        <option selected value="">Select One</option>

                                        @if($sale->order_status == \App\Models\Order::STATUS_PENDING)
                                            <option value="{{\App\Models\Order::STATUS_ORDER_PLACED}}" selected>{{ucwords(\App\Models\Order::STATUS_ORDER_PLACED)}}</option>
                                        @endif

                                        @if($sale->order_status == \App\Models\Order::STATUS_ORDER_PLACED )
                                            <option value="{{\App\Models\Order::STATUS_ORDER_PACKAGING}}" selected>{{ucwords(\App\Models\Order::STATUS_ORDER_PACKAGING)}}</option>
                                        @endif

                                        @if($sale->order_status == \App\Models\Order::STATUS_ORDER_PACKAGING)
                                            <option value="{{\App\Models\Order::STATUS_ORDER_PACKAGED}}" selected>{{ucwords(\App\Models\Order::STATUS_ORDER_PACKAGED)}}</option>
                                        @endif

                                        @if($sale->order_status == \App\Models\Order::STATUS_ORDER_PACKAGED && empty($sale->delivery_man_id))
                                            <option value="{{\App\Models\Order::STATUS_DELIVERY_ACCEPTED}}" selected>{{ucwords(\App\Models\Order::STATUS_DELIVERY_ACCEPTED)}}</option>
                                        @endif
                                        @if($sale->order_status == \App\Models\Order::STATUS_ORDER_PACKAGED && $sale->delivery_status == \App\Models\Order::STATUS_DELIVERY_ACCEPTED  && $sale->delivery_status !== \App\Models\Order::STATUS_DELIVERY_COLLECTED)
                                            <option value="{{\App\Models\Order::STATUS_DELIVERY_COLLECTED}}">{{ucwords(\App\Models\Order::STATUS_DELIVERY_COLLECTED)}}</option>
                                        @endif

                                        @if($sale->order_status == \App\Models\Order::STATUS_ORDER_PACKAGED && $sale->delivery_status == \App\Models\Order::STATUS_DELIVERY_COLLECTED  && $sale->delivery_status !== \App\Models\Order::STATUS_DELIVERY_DELIVERED)
                                            <option value="{{\App\Models\Order::STATUS_DELIVERY_DELIVERED}}">{{ucwords(\App\Models\Order::STATUS_DELIVERY_DELIVERED)}}</option>
                                        @endif

                                        @if($sale->order_status !== \App\Models\Order::STATUS_CANCEL)
                                            <option value="{{\App\Models\Order::STATUS_CANCEL}}">{{ucwords(\App\Models\Order::STATUS_CANCEL)}}</option>
                                        @endif
                                      </select>

                                  </div>
                                  @if($sale->order_status == \App\Models\Order::STATUS_ORDER_PACKAGED && empty($sale->delivery_man_id))
                                    <div class="mb-3">
                                        <label for="delivery_man_id" class="form-label">Assign Delivery Man</label>
                                        <select class="form-select" name="delivery_man_id" id="delivery_man_id" name="status" required>
                                            <option selected value="">Select One</option>
                                            @foreach ($deliveryMans as $deliveryMan)
                                            <option value="{{$deliveryMan->id}}" >{{$deliveryMan->first_name}}</option>

                                            @endforeach
                                        </select>

                                    </div>
                                  @endif
                                  <div class="mb-3">
                                    <button type="submit" class="btn btn-primary" id="formBtn">Submit</button>
                                  </div>
                            </form>
                            @endif
                            <div id="print_section">

                                @include("admin.order.partials.payment-details")
                            </div>
                        </div>
                        <div class="col-md-5">
                            @include("admin.order.partials.order-histories")
                        </div>
                    </div>



                    <div class="d-print-none row justify-content-between">
                        <div class="col-md-2">
                            <div class="d-flex d-sm-block justify-content-between justify-content-sm-start">
                                <a href="{{ route('orders.index') }}"
                                    class="btn btn-dark waves-effect waves-light">
                                    <i class="fa fa-arrow-left"></i> <span>Back</span>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="ic-print-header">
                                <a href="#" data-div-name="print-invoice" id="print_btn"
                                    class="btn btn-info waves-effect waves-light section-print-btn">
                                    <i class="fa fa-print"></i> <span>Print</span>
                                </a>
                                {{-- <a href="{{ route('order.invoice.download', $sale->id) }}"

                                    class="btn btn-primary waves-effect waves-light">
                                    <i class="fa fa-download"></i> <span>Download</span>
                                </a> --}}
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@push('style')

@endpush
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.print/1.6.2/jQuery.print.min.js"></script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '#formBtn', function() {
                if(confirm('Are you sure?')) {
                    $("#form").submit();
                }
            })
        })
    </script>
@endpush
