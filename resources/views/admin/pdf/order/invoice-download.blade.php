<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .text-center {
            text-align: center;
        }
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-style: normal !important;
        }
        .table th {
            background-color: #f2f2f2;
            text-align: left;
            font-style: normal !important;
        }
        .text-end {
            text-align: right;
        }
        .badge {
            padding: 5px 10px;
            color: #fff;
            border-radius: 4px;
        }
        .font-style-normal{
            font-style: normal !important;
        }
        .bg-info { background-color: #17a2b8; }
        .bg-danger { background-color: #dc3545; }
        .bg-warning { background-color: #ffc107; }
        .bg-success { background-color: #28a745; }
        .mb-0 {
            margin-bottom: 0;
        }
    </style>
</head>
<body>

<div class="container">



    <div class="invoice-title text-center">
        <h2>Invoice</h2>
    </div>
    <br> <!-- Optional spacing -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div id="print_section" class="card-body">
                    @include("admin.order.partials.payment-details",['isPdf'=>true])

                    <div class="row">
                        <div class="col-sm-12 mt-3">
                            <label for="">Payments</label>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Payment</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sale->order_payments as $payment)
                                        <tr>
                                            <td>{{$payment->date}}</td>
                                            <td>Type: {{ucwords($payment->payment_type)}}

                                                @if($payment->payment_type == 'bank' && !is_null($payment->account_info))
                                                Date: {{$payment->account_info['date'] ?? ''}} <br>
                                                Account No: {{$payment->account_info['account_no'] ?? ''}} <br>
                                                Transaction No: {{$payment->account_info['transaction_no'] ?? ''}} <br>
                                                Notes: {{$payment->notes ?? ''}} <br>

                                                @endif
                                            </td>
                                            <td>$ {{$payment->amount}}</td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>
