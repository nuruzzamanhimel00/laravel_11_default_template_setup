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
                                <th>Email:</th>
                                <td>{{$user->email}}</td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>{{$user->phone}}</td>
                            </tr>
                            <tr>
                                <th>Role:</th>
                                <td>{{$user->roles->first()->name ?? ''}}</td>
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
            <a href="{{route('administrations.index')}}" class="btn btn-primary">Back</a>
        </div>
    </div>
</div> <!-- end col -->
@endsection

@push('style')
@endpush
@push('script')
@endpush
