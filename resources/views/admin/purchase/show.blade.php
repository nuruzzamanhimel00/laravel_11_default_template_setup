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
                                    <h4 class="header-title">View Purchase</h4>
                                </div>
                                <div class="col-lg-6 d-print-none ic-print-btn-head text-end"><button
                                        data-div-name="section-to-print-pshow" type="button"
                                        class="btn btn-success section-print-btn" id="print_btn"><i class="fa fa-print"></i> Print</button>
                                    <a href="{{route('purchases.index')}}"
                                        class="btn btn-info mr-2"><i class="fa fa-arrow-left"></i> Back</a></div>
                            </div>
                            <div id="print_section">
                                <div class="table-responsive py-1">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <table class="table table-bordered">
                                                        <tbody>
                                                            <tr>
                                                                <td><b>Purchase Number : </b></td>
                                                                {{-- <td>:</td> --}}
                                                                <td>{{ $purchase->purchase_number }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Company :</b></td>
                                                                {{-- <td>:</td> --}}
                                                                <td>{{$purchase->supplier?->supplier?->company}} </td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Supplier Phone :</b></td>
                                                                {{-- <td>:</td> --}}
                                                                <td>{{$purchase->supplier?->phone}}</td>
                                                            </tr>

                                                            <tr>
                                                                <td><b>Warehouse :</b></td>
                                                                {{-- <td>:</td> --}}
                                                                <td>{{$purchase->warehouse?->name}}</td>
                                                            </tr>
                                                            {{-- <tr>
                                                                <td><b>Company</b></td>
                                                                <td>:</td>
                                                                <td>{{$purchase->company ?? ''}}</td>
                                                            </tr> --}}

                                                            {{-- <tr>
                                                                <td><b> Short address</b></td>
                                                                <td>:</td>
                                                                <td>{{$purchase->short_address}}</td>
                                                            </tr> --}}



                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td>

                                                    <table  class="table table-bordered">
                                                        <tbody>
                                                            <tr>
                                                                <tr>
                                                                    <td><b> Date :</b></td>
                                                                    {{-- <td>:</td> --}}
                                                                    <td>{{$purchase->date}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b> Note :</b></td>
                                                                    {{-- <td>:</td> --}}
                                                                    <td>{{$purchase->notes}}</td>
                                                                </tr>
                                                                {{-- <td><b>Address</b></td>
                                                                <td>:</td>
                                                                <td>{{$purchase->address}} </td>
                                                            </tr>

                                                            <tr>
                                                                <td><b>City</b></td>
                                                                <td>:</td>
                                                                <td>{{$purchase->city}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>
                                                                    Zip Code</b></td>
                                                                <td>:</td>
                                                                <td>{{$purchase->zipcode}}</td>
                                                            </tr> --}}

                                                            <tr>
                                                                <td><b>Status :</b></td>
                                                                {{-- <td>:</td> --}}
                                                                <td><span class="badge
                                                                    {{
                                                                    $purchase->status == App\Models\Purchase::STATUS_REQUESTED ? "bg-info" : ( $purchase->status == App\Models\Purchase::STATUS_CANCEL ? "bg-danger": "bg-success")
                                                                    }}
                                                                    ">{{ ucwords($purchase->status) }}</span></td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Received :</b></td>
                                                                {{-- <td>:</td> --}}
                                                                <td><span class="badge
                                                                    {{
                                                                    $purchase->purchase_receives->count() > 0 ? "bg-success" : "bg-warning"

                                                                    }}
                                                                    ">{{ $purchase->purchase_receives->count() > 0 ? 'RECEIVED' : 'NOT RECEIVED YET' }}</span></td>
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
                                                        <th>Note</th>
                                                        <th>Sub Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(!empty($purchase->purchase_items))
                                                    @foreach ($purchase->purchase_items as $item)
                                                    <tr>
                                                        <td>{{$loop->iteration}}</td>
                                                        <td>{{$item?->product?->sku}}</td>
                                                        <td>{{$item?->product?->barcode}}</td>
                                                        <td>{{  preg_replace('/\s*\(.*?\)\s*/', '', $item?->product?->name) }}
                                                        </td>

                                                        <td>{{$item->quantity}}</td>
                                                        <td class="text-right">{{addCurrency($item->price)}}</td>
                                                        <td>{{$item->notes}}</td>
                                                        <td class="text-right">{{addCurrency($item->sub_total)}}</td>
                                                    </tr>
                                                    @endforeach
                                                    @endif

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="7" class="text-right">Total: </th>
                                                        <th class="text-right">{{ addCurrency($purchase->purchase_items->sum('sub_total'))}}
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
