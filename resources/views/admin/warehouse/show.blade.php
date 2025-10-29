@extends('admin.layouts.app')

@section('content')
<div class="col-lg-12 p-0">
    <div class="card">
        <div class="card-body">
            {{-- {{dd($user->roles)}} --}}
            <div class="row">

                <div class="col-md-9">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Warehouse name:</th>
                                <td>{{$warehouse->full_name}}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{$warehouse->email}}</td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>{{$warehouse->phone}}</td>
                            </tr>
                            <tr>
                                <th>Company name:</th>
                                <td>{{$warehouse->company_name}}</td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td>{{$warehouse->address}}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>{!! $warehouse->status_badge !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{route('warehouses.index')}}" class="btn btn-primary">Back</a>
        </div>
    </div>
</div> <!-- end col -->
@endsection

@push('style')
@endpush
@push('script')
@endpush
