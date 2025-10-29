@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        @include('admin.common.users.show-orders')
    </div>
    <div id="my-modal"></div>
</div>
@endsection

@push('style')
<style>
    .card-header {
        padding: 1rem 1.25rem;
    }
    address {
        font-style: normal;
        line-height: 1.5;
    }
</style>
@endpush

@push('script')
@endpush
