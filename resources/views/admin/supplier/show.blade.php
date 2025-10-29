@extends('admin.layouts.app')
@section('content')
    <div class="col-lg-12 p-0">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <img src="{{$user->avatar_url}}" class="img-fluid rounded-circle border" alt="Profile Image">
                    </div>
                    <div class="col-md-9">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Company :</th>
                                    <td>{{$user?->supplier->company ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{$user->email}}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{$user->phone}}</td>
                                </tr>
                                <tr>
                                    <th>Name :</th>
                                    <td>{{$user->first_name ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>Designation :</th>
                                    <td>{{$user?->supplier->designation ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>Address :</th>
                                    <td>{{$user?->supplier->address ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>City :</th>
                                    <td>{{$user?->supplier->city ?? ""}}</td>
                                </tr>
                                <tr>
                                    <th>Zip Code :</th>
                                    <td>{{$user?->supplier->zipcode ?? ""}}</td>
                                </tr>
                                <tr>
                                    <th>Short Address:</th>
                                    <td>{{$user?->supplier->short_address ?? ""}}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>{!! $user->status_badge !!}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{route('suppliers.index')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div> <!-- end col -->
@endsection


@push('style')
@endpush

@push('script')
@endpush
