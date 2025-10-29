@extends('admin.layouts.app')

@section('content')
<div class="card mb-2">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-sm-10">
                        <form action="{{route('orders.reports.index')}}" method="get">
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
                        <form action="{{route('orders.reports.index')}}?q=all-time">
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
                @if($orders->count() > 0)
                <div class="d-flex justify-content-between">
                    <div class="d-flex flex-column">
                        <p class="mb-0"> <b>Sales Report:</b>
                            @if(is_null($all_time))
                            {{$from_date}} - {{$to_date}}
                            @else
                            All Time
                            @endif
                        </p>
                        <p class="mb-0"><b>Gross Total:</b>{{getCurrency()}}
                            {{formatNumberSmart($orders->sum('total'))}} </p>
                        <p class="mb-0"> <b>Paid Total:</b>{{getCurrency()}}
                            {{formatNumberSmart($orders->sum('total_paid'))}} </p>
                        <p class="mb-0"><b>Due :</b>
                            {{getCurrency()}} {{
                                    formatNumberSmart(    $orders->sum(function ( $order) {
                                                        return (float)$order->total - (float)$order->total_paid;
                                                    })
                                    )
                                }}
                        </p>


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
                                <tr class="ic_table">
                                    <th>#SL</th>
                                    <th>Invoice ID</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Tax/Vat ({{getCurrency()}})</th>
                                    <th>Total ({{getCurrency()}})</th>
                                    <th>Due ({{getCurrency()}})</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($orders->count() > 0)
                                @foreach ($orders as $order)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>
                                        <a href="{{route('orders.show',$order->id)}}"> {{$order->invoice_no}}</a>
                                    </td>
                                    <td>{{$order->date}}</td>
                                    <td>{{$order?->customer->full_name?? ''}}({{ucwords($order?->customer->type)}})</td>
                                    <td>{{formatNumberSmart($order->tax_amount)}}</td>
                                    <td>{{formatNumberSmart($order->total)}}</td>
                                    <td>{{ formatNumberSmart((float)$order->total -  (float)$order->total_paid)}}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="5">Total:</td>
                                    <td> {{formatNumberSmart($orders->sum('total'))}} </td>
                                    <td>
                                        {{

                                                    formatNumberSmart($orders->sum(function ( $order) {
                                                        return (float)$order->total - (float)$order->total_paid;
                                                    }))
                                                }}

                                    </td>
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
