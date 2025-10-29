@extends('admin.layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive ic-datatable">
                {!! $dataTable->table() !!}
            </div>
        </div>
    </div>
@endsection

@push('style')
    @include('admin.layouts.partials.datatableCss')
@endpush
@push('script')
    @include('admin.layouts.partials.dataTablejs')
@endpush
