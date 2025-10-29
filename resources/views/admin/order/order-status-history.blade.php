@extends('admin.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-body">
                    <div class="d-print-none row justify-content-end">
                        <div class="col-md-2">
                            <div class="d-flex d-sm-block justify-content-end">
                                <a href="{{ url()->previous() }}"
                                    class="btn btn-dark waves-effect waves-light">
                                    <i class="fa fa-arrow-left"></i> <span>Back</span>
                                </a>
                            </div>
                        </div>

                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            @include("admin.order.partials.order-histories")
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

@endpush
