@extends('admin.layouts.app')

@section('content')

<div class="card mb-0" id="print_section">
    <div class="card-body">
        <div class="row">
            <div class="col-12">

                <div class="table-responsive">

                    <table class="table table-sm table-bordered table-striped nowrap">
                        <thead>
                            <tr>
                                <th>#SL</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Purchase Price ({{getCurrency()}})</th>
                                <th>User Price ({{getCurrency()}})</th>
                                <th>Restaurant Price ({{getCurrency()}})</th>
                                <th>Low Stock Alert</th>
                                <th>Stock</th>


                            </tr>
                        </thead>
                        <tbody>
                            @if($products->count() > 0)
                            @foreach ($products as $product)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>
                                    <a href="{{route('products.edit',$product->id)}}"> {{$product->name}}</a> <br>
                                    <span><b>SKU: </b> {{$product->sku}} </span> <br>
                                    <span><b>Barcode: </b> {{$product->barcode}} </span> <br>
                                </td>
                                <td>{{$product?->category->name ?? null}}</td>
                                <td>{{$product?->brand->name ?? null}}</td>
                                <td> {{formatNumberSmart($product->purchase_price)}} </td>
                                <td> {{formatNumberSmart($product->sale_price)}} </td>
                                <td> {{formatNumberSmart($product->restaurant_sale_price)}} </td>

                                <td>{{LOW_STOCK_ALERT}} </td>
                                <td>{{formatNumberSmart($product?->warehouse_stock?->stock_quantity) ?? 0}} </td>

                            </tr>
                            @endforeach

                            @endif
                        </tbody>
                    </table>
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
