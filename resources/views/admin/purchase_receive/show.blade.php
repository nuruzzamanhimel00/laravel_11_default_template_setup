@extends('admin.layouts.app')

@section('content')
<div class="col-lg-12 p-0">
    <div class="card">
        <div class="card-body">
            {{-- <h4 class="header-title">{{ __('Show Product ') }}</h4> --}}
            <div class="container">
                <!-- Product Details -->
                <div class="card mb-4">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h4 class="header-title">Purchase Receive Details</h4>
                            </div>
                            <div class="col-lg-6 d-print-none ic-print-btn-head text-end"><button
                                    data-div-name="section-to-print-pshow" type="button"
                                    class="btn btn-success section-print-btn" id="print_btn"><i class="fa fa-print"></i>
                                    Print</button>
                                <a href="{{route('purchases.receive.index')}}" class="btn btn-info mr-2"><i
                                        class="fa fa-arrow-left"></i> Back</a>
                            </div>
                        </div>
                        <div id="print_section" class="mt-3">
                            <ul class="list-group">
                                <li class="list-group-item p-1">Cras justo odio</li>
                                <li class="list-group-item p-1">Dapibus ac facilisis in</li>
                                <li class="list-group-item p-1">Morbi leo risus</li>
                                <li class="list-group-item p-1">Porta ac consectetur ac</li>
                                <li class="list-group-item p-1">Vestibulum at eros</li>
                            </ul>
                            <div class="table-responsive py-2">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <table width="100%" cellpadding="0" cellspacing="0"
                                                    class="ic-purchase-print">
                                                    <tbody>
                                                        <tr>
                                                            <td><b>Purchase Number </b></td>
                                                            <td>:</td>
                                                            <td>{{ $purchaseReceive->purchase->purchase_number }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Supplier</b></td>
                                                            <td>:</td>
                                                            <td>{{$purchaseReceive->purchase->supplier?->full_name}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Supplier Phone</b></td>
                                                            <td>:</td>
                                                            <td>{{$purchaseReceive->purchase->supplier?->phone}}</td>
                                                        </tr>

                                                        <tr>
                                                            <td><b>Warehouse</b></td>
                                                            <td>:</td>
                                                            <td>{{$purchaseReceive->purchase->warehouse?->name}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Company</b></td>
                                                            <td>:</td>
                                                            <td>{{$purchaseReceive->purchase->company}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Receive Date</b></td>
                                                            <td>:</td>
                                                            <td>{{$purchaseReceive->receive_date}}</td>
                                                        </tr>



                                                    </tbody>
                                                </table>
                                            </td>
                                            <td>
                                                <table width="100%" cellpadding="0" cellspacing="0"
                                                    class="ic-purchase-print">
                                                    <tbody>

                                                        <tr>
                                                            <td><b>Address</b></td>
                                                            <td>:</td>
                                                            <td>{{$purchaseReceive->purchase->address}} </td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>City</b></td>
                                                            <td>:</td>
                                                            <td>{{$purchaseReceive->purchase->city}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Zipcode</b></td>
                                                            <td>:</td>
                                                            <td>{{$purchaseReceive->purchase->zipcode}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Note</b></td>
                                                            <td>:</td>
                                                            <td>{{$purchaseReceive->purchase->notes}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Short address</b></td>
                                                            <td>:</td>
                                                            <td>{{$purchaseReceive->purchase->short_address}}</td>
                                                        </tr>


                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>SL</th>
                                                    <th>SKU</th>
                                                    <th>Barcode</th>
                                                    <th>Product Name</th>
                                                    <th>Size</th>
                                                    <th>Condition</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>

                                                    <th>Sub Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(!empty($itemList))
                                                @foreach ($itemList as $key => $item)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$item['product_sku']}}</td>
                                                    <td>{{$item['barcode']}}</td>
                                                    <td>{{  $item['name'] }}
                                                    </td>
                                                    <td>{{$item['size']}}</td>
                                                    <td>{{$item['condition_name']}}</td>
                                                    <td>{{$item['quantity']}}</td>
                                                    <td class="text-right">{{ addCurrency($item['price'])}}</td>

                                                    <td class="text-right">{{ addCurrency($item['sub_total'])}}</td>
                                                </tr>
                                                @endforeach
                                                @endif

                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="8" class="text-right">Total: </th>
                                                    <th class="text-right">
                                                        {{addCurrency(collect($itemList)->sum('sub_total'))}}
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div> <!-- end col -->
@endsection

@push('style')
@endpush
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.print/1.6.2/jQuery.print.min.js"></script>
@endpush