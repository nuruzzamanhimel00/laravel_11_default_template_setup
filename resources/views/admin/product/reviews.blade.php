@extends('admin.layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="ic-datatable">
                {!! $dataTable->table(['class' => 'nowrap']) !!}
                <form action="{{ route('products.barcode.download.zip') }}" method="post" id="download_form" style="display: none">
                    @csrf
                    <input type="text" name="product_ids" id="product_ids">
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    @include('admin.layouts.partials.datatableCss')
    <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"
    />
@endpush
@push('script')
    @include('admin.layouts.partials.dataTablejs')
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        Fancybox.bind('[data-fancybox]', {
          //
        });
      </script>

@endpush
