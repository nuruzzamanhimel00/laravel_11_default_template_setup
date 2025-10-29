<table class="table">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Date</th>
        <th scope="col">Amount ({{getCurrency()}})</th>
        <th scope="col">Payment Type</th>
        <th scope="col">Notes</th>
        @if($sale->is_split_sale)
        <th scope="col">Action</th>
        @endif
      </tr>
    </thead>
    <tbody>
        @if($sale->order_payments->count() > 0)
            @foreach ($sale->order_payments()->paginate(10) as $payment )
                <tr>
                    <th scope="row">{{$loop->iteration}}</th>
                    <td>{{$payment->date_formate}}</td>
                    <td> {{$payment->amount}}</td>
                    <td>{{ ucwords($payment->payment_type) }}</td>
                    <td>{{$payment->notes}}</td>
                    @if($sale->is_split_sale)
                    <td>
                        <a class="btn btn-success btn-sm" href="{{route('order.payment.edit',['id' => $sale->id,'pid' => $payment->id])}}"><i class="mdi mdi-square-edit-outline"></i></a>
                        @if($sale->order_payments->count() > 1)
                        <form action="{{ route('order.payment.delete', ['id' => $sale->id,'pid' => $payment->id]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="button" title="Delete"
                                onclick="if(confirm('Are you sure you want to delete this item?')) { this.closest('form').submit(); }">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                    @endif
                </tr>
            @endforeach
        @endif


    </tbody>
  </table>
<div id="paginate_section">
    {!! $sale->order_payments()->paginate(10)->links('vendor.pagination.bootstrap-5') !!}

</div>
