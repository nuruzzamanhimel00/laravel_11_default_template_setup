@extends('admin.layouts.app')

@section('content')
<div class="card mb-2">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-sm-10">
                        <form action="{{route('purchases.reports.index')}}" method="get">
                            <div class="row input-daterange">

                                <div class="col-md-4 col-lg-3">
                                    <div class="form-group mb-lg-0"><input type="date" name="from_date"
                                            value="{{is_null($all_time) ? $from_date: ''}}" id="from_date"
                                            placeholder="From Date" autocomplete="off" required="required"
                                            class="ic_form_control"></div>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <div class="form-group mb-lg-0"><input type="date" name="to_date"
                                            value="{{is_null($all_time) ? $to_date : ''}}" id="to_date"
                                            placeholder="To Date" min="{{$from_date}}" autocomplete="off"
                                            required="required" class="ic_form_control"></div>
                                </div>
                                <div class="col-md-4 col-lg-3 col-12"><button type="submit"
                                        class="btn btn-primary w-100"><i class="mdi mdi-filter"></i>
                                        Generate</button></div>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-2">
                        <form action="{{route('purchases.reports.index')}}?q=all-time">
                            <div class="input-daterange"><input type="hidden" name="warehouse" value=""
                                    id="allTimeWarehouse"> <input type="hidden" name="q" value="all-time"> <button
                                    type="submit" class="btn btn-secondary w-100"><i class="mdi mdi-filter"></i> All
                                    Time</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="card mb-0" id="print_section">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                @if($purchases->count() > 0)
                @php
                $total_quantity = $purchases->reduce(function ($carry, $item) {
                return $carry + $item->purchase_items->sum('quantity');
                }, 0) ?? 0;
                $total_receive_quantity = $purchases->reduce(function ($carry, $item) {
                return $carry + $item->purchase_items->sum('receive_quantity');
                }, 0) ?? 0;

                @endphp
                <div class="d-flex justify-content-between">
                    <div class="d-flex flex-column">
                        <p class="mb-0"> <b>Sales Report:</b>
                            @if(is_null($all_time))
                            {{$from_date}} - {{$to_date}}
                            @else
                            All Time
                            @endif
                        </p>
                        <p class="mb-0"><b> Total Quantity:</b>{{formatNumberSmart($total_quantity) ?? 0}} </p>



                    </div>
                    <div class="text-end d-flex align-items-start">
                        <hr>
                        <button type="button" data-div-name="section-to-print-payments"
                            class="btn btn-warning btn-sm section-print-btn" id="print_btn">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                </div>
                @endif
                <div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm table-bordered table-striped nowrap">
                            <thead>
                                <tr>
                                    <th>#SL</th>
                                    <th>Purchase Number</th>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th>Total ({{ getCurrency() }})</th>
                                    <th>Total Product</th>
                                    <th>Total Quantity</th>
                                    <th>Total Receive</th>
                                    <th>Total Return</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $grandTotal = 0;
                                $totalQuantity = 0;
                                $totalReceiveQuantity = 0;
                                $totalReturnQuantity = 0;
                                @endphp

                                @if($purchases->count() > 0)
                                @foreach ($purchases as $purchase)
                                @php
                                $totalReceiveItemCount = $purchase->purchase_items->sum(fn($item) =>
                                $item->purchase_receive_items->sum('quantity'));
                                $totalReturnItemCount = $purchase->purchase_items->sum(fn($item) =>
                                $item->purchase_return_items->sum('quantity'));

                                $grandTotal += $purchase->total;
                                $totalQuantity += $purchase->purchase_items->sum('quantity');
                                $totalReceiveQuantity += $totalReceiveItemCount;
                                $totalReturnQuantity += $totalReturnItemCount;
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><a
                                            href="{{ route('purchases.show', $purchase->id) }}">{{ $purchase->purchase_number }}</a>
                                    </td>
                                    <td>{{ $purchase->date }}</td>
                                    <td>{{ $purchase->supplier->supplier->company ?? '' }}</td>
                                    <td>{{ formatNumberSmart($purchase->total) }}</td>
                                    <td>{{ $purchase->purchase_items->count() }}</td>
                                    <td>{{ $purchase->purchase_items->sum('quantity') }}</td>
                                    <td>{{ $totalReceiveItemCount }}</td>
                                    <td>{{ $totalReturnItemCount }}</td>
                                </tr>
                                @endforeach

                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total:</td>
                                    <td class="fw-bold">{{ $grandTotal }}</td>
                                    <td></td>
                                    <td class="fw-bold">{{ $totalQuantity }}</td>
                                    <td class="fw-bold">{{ $totalReceiveQuantity }}</td>
                                    <td class="fw-bold">{{ $totalReturnQuantity }}</td>
                                </tr>
                                @else
                                <tr>
                                    <td colspan="9" class="text-center">No purchases found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
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
    $(document).on('change', '#from_date', function() {
        let from_date = new Date($(this).val());
        if (!isNaN(from_date)) {
            // Add 1 day
            let next_day = new Date(from_date);
            next_day.setDate(from_date.getDate() + 1);

            // Format to YYYY-MM-DD
            let yyyy = next_day.getFullYear();
            let mm = ('0' + (next_day.getMonth() + 1)).slice(-2);
            let dd = ('0' + next_day.getDate()).slice(-2);
            let formatted = `${yyyy}-${mm}-${dd}`;

            $('#to_date').attr('min', $(this).val());
            $('#to_date').val(formatted);
        }
    });
});
</script>

@endpush
