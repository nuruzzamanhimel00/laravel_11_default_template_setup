@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div id="print_section" class="card-body">
                @include("admin.order.partials.payment-details")

                <div class="row d-print-none">
                    <div class="col-sm-8 mt-3">
                        <form action="{{route('order.payment.update',[
                        'sale' => $sale,
                        'salePayment' => $salePayment
                        ])}}" method="POST" data-parsley-validate>
                            @csrf
                            <label for="" class="w-100">Payment</label>
                            <div class="ic-payment-method d-flex">
                                <div class="payment-method me-1">
                                    <input type="radio" value="cash" id="cash" name="payment_type" class="payment_type_input" {{
                                        $salePayment->payment_type == 'cash' ? 'checked' : ''
                                    }} >
                                    <label for="cash" class="radio-inline radio-image">
                                        <span></span>
                                        <img src="{{asset('images/default/cash.png')}}" alt="images" width="80">
                                    </label>
                                </div>

                                <div class="payment-method me-1">
                                    <input type="radio" value="bank" id="bank" name="payment_type" class="payment_type_input"
                                    {{
                                        $salePayment->payment_type == 'bank' ? 'checked' : ''
                                    }} >
                                    <label for="bank" class="radio-inline radio-image">
                                        <span></span>
                                        <img src="{{asset('images/default/bank.png')}}" alt="images" class="ic-paypal" width="100">
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Amount</label>
                                <div class="ic-copy-url">
                                    <div class="input-group mb-3">
                                        <input name="total_paid" type="number" min="0" step="any" required class="form-control payment_amount" value="{{$salePayment->amount}}">

                                    </div>
                                </div>
                            </div>

                            <div id="bank-info">
                                @if($salePayment->payment_type == 'bank')
                                    <div class="form-group">
                                        <label for="">Account Number <span class="error">*</span></label>
                                        <input type="text" name="payment_info[account_no]" class="form-control" value="{{$salePayment->account_info['account_no']}}" required>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label for="">Transaction No <span class="error">*</span></label>
                                        <input type="text" name="payment_info[transaction_no]" class="form-control" value="{{$salePayment->account_info['transaction_no']}}" required>
                                    </div>
                                    <div class="form-group mt-3 mb-3">
                                        <label for="">Transaction Date <span class="error">*</span></label>
                                        <input type="date" name="payment_info[date]" class="form-control " value="{{$salePayment->account_info['date']}}" required>
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label>Note</label>
                                <textarea cols="30" rows="10" class="form-control"  name="notes">{{$salePayment->notes ?? ''}}</textarea>
                              </div>
                            <div class="input-group-append mt-2">
                                <button type="submit" class="btn btn-primary">Update Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="d-print-none row justify-content-between mt-3">
                    <div class="col-md-2">
                        <div class="d-flex d-sm-block justify-content-between justify-content-sm-start">
                            <a href="{{route('orders.index')}}" class="btn btn-dark waves-effect waves-light">
                                <i class="fa fa-arrow-left"></i> <span>Back</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="ic-print-header">
                            <a href="#" data-div-name="print-invoice" id="print_btn" class="btn btn-info waves-effect waves-light section-print-btn">
                                <i class="fa fa-print"></i> <span>Print</span>
                            </a>
                            {{-- <a href="{{route('orders.invoice.download', $sale->id)}}" class="btn btn-primary waves-effect waves-light">
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
        let total_due_amount = {{ ($sale->total - $sale->total_paid) + $salePayment->amount }};

        console.log('total_due_amount',total_due_amount)
        $(document).on('blur', '.payment_amount', function () {
            let value = parseFloat($(this).val()); // Convert input value to a float
            let totalDueAmount = parseFloat(total_due_amount); // Ensure total_due_amount is a float

            // console.log('value', value);

            if (value > totalDueAmount) {
                alert('Paid Amount should not be greater than the total due amount.');
                $(this).val(totalDueAmount.toFixed(2)); // Set the value to the total due amount with 2 decimal places
            }
        });
        $(document).on('change','.payment_type_input', function(){
            let value = $(this).val();
            if(value == 'cash'){
                $("#bank-info").empty();
            }else{
                $("#bank-info").html(`
                    <div class="form-group">
                        <label for="">Account Number <span class="error">*</span></label>
                        <input type="text" name="payment_info[account_no]" class="form-control" required>
                    </div>
                    <div class="form-group mt-3">
                        <label for="">Transaction No <span class="error">*</span></label>
                        <input type="text" name="payment_info[transaction_no]" class="form-control" required>
                    </div>
                    <div class="form-group mt-3 mb-3">
                        <label for="">Transaction Date <span class="error">*</span></label>
                        <input type="date" name="payment_info[date]" class="form-control " required>
                    </div>
                `)
            }
            console.log('value',value)
        })
    })
</script>
@endpush
