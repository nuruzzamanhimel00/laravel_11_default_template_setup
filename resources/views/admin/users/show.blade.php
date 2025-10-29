@extends('admin.layouts.app')

@section('content')
<div class="col-lg-12 p-0">
    <div class="card">
        <div class="card-body">
            {{-- {{dd($user->roles)}} --}}
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="{{$user->avatar_url}}" class="img-fluid rounded-circle border" alt="Profile Image">
                </div>
                <div class="col-md-9">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Name:</th>
                                <td>{{$user->full_name}}</td>
                            </tr>
                            <tr>
                                <th>User Name:</th>
                                <td>{{$user->username}}</td>
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
                                <th>Status:</th>
                                <td>{!! $user->status_badge !!}</td>
                            </tr>
                            <tr>
                                <th>Total Orders:</th>
                                <td>{{ $user->orders->count() }}</td>
                            </tr>
                            <tr>
                                <th>Total {{ucwords(\App\Models\Order::STATUS_DELIVERY_COMPLETE)}} Order:</th>
                                <td>{{
                                $user
                                ->orders
                                ->where('order_status','!=', \App\Models\Order::STATUS_CANCEL)
                                ->where('delivery_status', \App\Models\Order::STATUS_DELIVERY_COMPLETE)
                                ->count() }}</td>
                            </tr>
                            <tr>
                                <th>Total {{ucwords(\App\Models\Order::STATUS_CANCEL)}} Order:</th>
                                <td>{{  $user->orders->where('order_status', \App\Models\Order::STATUS_CANCEL)->count() }}</td>
                            </tr>
                            <tr>
                                <th>Total Price:</th>
                                <td>{{ addCurrency($user->orders->sum('total')?? 0) }}</td>
                            </tr>
                            <tr>
                                <th>Total Paid:</th>
                                <td>{{ addCurrency($user->orders->sum('total_paid')?? 0) }}</td>
                            </tr>
                            <tr>
                                <th>Total Due:</th>
                                <td>{{  addCurrency((float)$user->orders->sum('total') - (float)$user->orders->sum('total_paid')) }}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{route('users.index')}}" class="btn btn-primary">Back</a>
        </div>
    </div>
</div> <!-- end col -->
@endsection

@push('style')
@endpush
@push('script')
@endpush
