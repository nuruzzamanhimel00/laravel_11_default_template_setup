@extends('admin.layouts.app')

@section('content')
    <div class="col-lg-12 p-0">
        <form action="{{ route('orders.store') }}" method="post" enctype="multipart/form-data" id="saleForm"
            data-parsley-validate>
            @csrf

            <div id="vueApp">
                <create-order :regular_users="{{ json_encode($regular_users) }}" :restaurants="{{ json_encode($restaurants) }}" :currency="{{ json_encode(getCurrency()) }}" :order_for="{{ json_encode($order_for) }}" :order_id="{{ json_encode($order_id) }}" />
            </div>



        </form>
    </div> <!-- end col -->
@endsection

@push('style')
    {{-- <link rel="stylesheet" href="https://unpkg.com/vue-multiselect/dist/vue-multiselect.min.css"> --}}
@endpush
@push('script')
<script>
$(document).ready(function () {
    $(document).on('change', '.order_for_radio', function () {
        let value = $(this).val()
        let url = "{{ url()->current() }}?order_for=" + value
        window.location.href = url
        console.log(value,  url)
    })
})
</script>
@endpush
