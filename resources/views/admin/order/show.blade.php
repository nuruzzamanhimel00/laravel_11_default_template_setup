@extends('admin.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div id="print_section" class="card-body">
                    @include("admin.order.partials.payment-details")

                    <div class="row">
                        <div class="col-sm-12 mt-3">
                            <label for="">Payments</label>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Payment</th>
                                            <th>Amount ({{getCurrency()}})</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sale->order_payments as $payment)
                                        <tr>
                                            <td>{{$payment->date_formate}}</td>
                                            <td>Type: {{ucwords($payment->payment_type)}}

                                                @if($payment->payment_type == 'bank' && !is_null($payment->account_info))
                                                Date: {{$payment->account_info['date'] ?? ''}} <br>
                                                Account No: {{$payment->account_info['account_no'] ?? ''}} <br>
                                                Transaction No: {{$payment->account_info['transaction_no'] ?? ''}} <br>
                                                Notes: {{$payment->notes ?? ''}} <br>

                                                @endif
                                            </td>
                                            <td>{{$payment->amount}}</td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
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
                                <a href="{{ route('order.invoice.download', $sale->id) }}"
                                {{-- <a href="#" --}}
                                    class="btn btn-primary waves-effect waves-light">
                                    <i class="fa fa-download"></i> <span>Download</span>
                                </a>
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

        })
    </script>
@endpush
