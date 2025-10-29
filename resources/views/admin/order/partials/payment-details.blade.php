<div class="row">
    <div class="col-12">

        <div>
            <table width="100%" cellpadding="0" cellspaceing="0" class="mb-5 border-0">
                <tbody class="border-0">
                    <tr class="border-0">
                        <td class="border-0">
                            <div class="invoice-title">
                                {{-- {{dd($isPdf)}} --}}
                                @if(isset($isPdf) && $isPdf == true)
                                {{-- {{dd(config('settings.site_logo'))}} --}}
                                <img
                                src="{{  config('settings.site_logo') ? public_path('storage/'.config('settings.site_logo')) : public_path('images/default/logo-dark.png')}}"
                                alt="logo" style="max-width: 200px;" width="100"
                                class="ic-logo-height img-fluid">

                                @else
                                <img
                                src="{{ config('settings.site_logo') ? getStorageImage(config('settings.site_logo'),false,'logo') : getDefaultLogo()}}"
                                alt="logo" style="max-width: 200px;" width="100"
                                class="ic-logo-height img-fluid">
                                @endif
                            </div>
                        </td>
                        <td class="border-0" style="float: right; text-align: right;">
                            <h5 style="margin: 0px; text-transform: uppercase; letter-spacing: 3px;">{{config('settings.site_title') ?? 'Grozaar' }}</h5>
                            {{-- <p
                                style="margin: 0px; max-width: 175px; white-space: break-spaces; font-size: 14px; letter-spacing: 2px; text-transform: uppercase;">
                                Dhaka-1230, Bangladesh</p>
                            <p
                                style="margin: 0px; text-transform: uppercase; font-size: 14px; letter-spacing: 2px;">
                                VAT:12345678</p> --}}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="table-responsive">

                <table class="table" cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tbody>
                        <tr>
                            @if (!is_null($sale->billing_info))
                                <td>
                                    <address class="ic-invoice-addess">
                                        <strong class="font-style-normal">Billed To:</strong><br>
                                        <p class="mb-0 font-style-normal">{{ $sale->billing_info['full_name'] ?? '' }}</p>
                                        <p class="mb-0 font-style-normal">{{ $sale->billing_info['email'] ?? '' }}</p>
                                        <p class="mb-0 font-style-normal">{{ $sale->billing_info['phone'] ?? '' }}</p>
                                        <p class="mb-0 font-style-normal">{{ $sale->billing_info['address'] ?? '' }}</p>
                                    </address>
                                </td>
                            @endif
                            @if (!is_null($sale->shipping_info))
                                <td>
                                    <address class="ic-invoice-addess">
                                        <strong class="font-style-normal">Shipped To:</strong>
                                        <p class="mb-0 font-style-normal">{{ $sale->shipping_info['full_name'] ?? '' }}</p>
                                        <p class="mb-0 font-style-normal">{{ $sale->shipping_info['email'] ?? '' }}</p>
                                        <p class="mb-0 font-style-normal">{{ $sale->shipping_info['phone'] ?? '' }}</p>
                                        <p class="mb-0 font-style-normal">{{ $sale->shipping_info['address'] ?? '' }}</p>
                                    </address>
                                </td>
                            @endif
                            <td>
                                @php
                                    $badge =
                                        $sale->order_status == \App\Models\Order::STATUS_PENDING
                                            ? 'bg-info'
                                            : ($sale->order_status == \App\Models\Order::STATUS_CANCEL
                                                ? 'bg-danger'
                                                : ($sale->order_status == \App\Models\Order::STATUS_PARTIALLY_PAID
                                                    ? 'bg-warning'
                                                    : 'bg-success'));
                                @endphp
                                <address class="ic-invoice-addess text-end">
                                    <strong class="font-style-normal">Invoice:</strong>
                                    <p class="mb-0 font-style-normal">Invoice ID # {{ $sale->invoice_no }}</p>
                                    <p class="mb-0 font-style-normal">Date: {{ $sale->date }}</p>
                                    <p class="mb-0 font-style-normal">Total:  {{ $sale->total }} ({{getCurrency()}})</p>
                                    <p class="mb-0 font-style-normal">Order Status: {{ ucwords(str_replace('_', ' ', $sale->order_status)) }}</p>
                                    <p class="mb-0 font-style-normal">Payment Status: {{ ucwords(str_replace('_', ' ', $sale->payment_status)) }}</p>
                                    {{-- @if(!is_null($sale->delivery_status)) --}}
                                    <p class="mb-0 font-style-normal">Delivery Status: {{  !is_null($sale->delivery_status) ? ucwords(str_replace('_', ' ', $sale->delivery_status)) : 'N\A' }}</p>
                                    {{-- @endif --}}
                                </address>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div>
            <div class="p-2">
                <h3 class="font-16"><strong>Summary</strong></h3>
            </div>
            <div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><strong>SKU</strong></th>
                                <th><strong>Name</strong></th>
                                <th><strong>Warehouse</strong></th>

                                <th><strong>Quantity</strong></th>
                                <th><strong>Unit Price</strong></th>
                                <th><strong>Discount ({{getCurrency()}})</strong></th>

                                <th><strong> Total ({{getCurrency()}})</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->order_items as $item)
                                <tr>
                                    <td>{{ $item->product_sku }}</td>
                                    <td>{{ $item->product_name ?? '' }}</td>
                                    <td>{{ $item->warehouse->name ?? '' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->price }}</td>
                                    <td>{{ $item->discount }} - {{ !is_null($item->discount_type) ? '('. $item->discount_type. ')' : '' }}</td>


                                    <td> {{ $item->sub_total }}</td>
                                </tr>
                            @endforeach

                            <tr>

                                <td class="thick-line text-end" colspan="6"><strong>Sub Total</strong></td>
                                <td class="thick-line"> {{$sale->order_items->sum('sub_total')}}</td>
                            </tr>

                            <tr>

                                <td class="thick-line text-end" colspan="6"><strong>Discount</strong></td>
                                <td class="thick-line">{{$sale->discount_amount}}</td>
                            </tr>
                            <tr>

                                <td class="thick-line text-end" colspan="6"><strong>Tax/Vat</strong></td>
                                <td class="thick-line">{{$sale->tax_amount}}</td>
                            </tr>
                            @if($sale->platform == \App\Models\Order::PLATFORM_MOBILE)
                            <tr>

                                <td class="thick-line text-end" colspan="6"><strong>Delivery Cost</strong></td>
                                <td class="thick-line">{{$sale->delivery_cost}}</td>
                            </tr>
                            @endif



                            <tr>

                                <td class="no-line text-end" colspan="6"><strong>Total</strong></td>
                                <td class="no-line">
                                    <p class="mb-0"><b> {{ $sale->total }}</b></p>
                                </td>
                            </tr>
                            <tr>


                                <td class="no-line text-end" colspan="6"><strong>Total Paid</strong></td>
                                <td class="no-line">
                                    <p class="mb-0"><b> {{ $sale->total_paid }}</b></p>
                                </td>
                            </tr>
                            <tr>


                                <td class="no-line text-end" colspan="6"><strong>Total Due</strong></td>
                                <td class="no-line">
                                    <p class="mb-0"><b>{{ $sale->total - $sale->total_paid }}</b></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
