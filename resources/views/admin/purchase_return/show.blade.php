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
                                    <h4 class="header-title">Purchase Return Details</h4>
                                </div>
                                <div class="col-lg-6 d-print-none ic-print-btn-head text-end"><button
                                        data-div-name="section-to-print-pshow" type="button"
                                        class="btn btn-success section-print-btn" id="print_btn"><i class="fa fa-print"></i> Print</button>
                                    <a href="{{route('purchases.return.index')}}"
                                        class="btn btn-info mr-2"><i class="fa fa-arrow-left"></i> Back</a></div>
                            </div>
                            <div id="print_section">
                                <div class="table-responsive py-2">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <table  class="table table-bordered">
                                                        <tbody>
                                                            <tr>
                                                                <td><b>Purchase Number </b></td>
                                                                <td>:</td>
                                                                <td>{{ $purchaseReturn->purchase->purchase_number }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Supplier</b></td>
                                                                <td>:</td>
                                                                <td>{{$purchaseReturn->purchase->supplier?->full_name}}</td>
                                                            </tr>

                                                            <tr>
                                                                <td><b>Warehouse</b></td>
                                                                <td>:</td>
                                                                <td>{{$purchaseReturn->purchase->warehouse?->name}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Return Date</b></td>
                                                                <td>:</td>
                                                                <td>{{$purchaseReturn->return_date}}</td>
                                                            </tr>



                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td>
                                                    <table  class="table table-bordered">
                                                        <tbody>
                                                            <tr>
                                                                <td><b>Return Note</b></td>
                                                                <td>:</td>
                                                                <td>{{$purchaseReturn->note}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Address Line 1 </b></td>
                                                                <td>:</td>
                                                                <td>{{$purchaseReturn->purchase->address_line_1}} </td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Address Line 2</b></td>
                                                                <td>:</td>
                                                                <td>{{$purchaseReturn->purchase->address_line_2}}</td>
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

                                                        <td>{{$item['quantity']}}</td>
                                                        <td class="text-right">{{getCurrency()}} {{$item['price']}} </td>

                                                        <td class="text-right">{{getCurrency()}} {{$item['sub_total']}} </td>
                                                    </tr>
                                                    @endforeach
                                                    @endif

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="6" class="text-right">Total: </th>
                                                        <th class="text-right">{{getCurrency()}} {{collect($itemList)->sum('sub_total')}}
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
