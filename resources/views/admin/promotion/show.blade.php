@extends('admin.layouts.app')

@section('content')
<div class="col-lg-12 p-0">
    <div class="card">
        <div class="card-body">
            {{-- {{dd($user->roles)}} --}}
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="{{$promotion->image_url}}" class="img-fluid rounded-circle border" alt="Profile Image">
                </div>
                <div class="col-md-9">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>User Type':</th>
                                <td>{{$promotion->target_type}}</td>
                            </tr>
                            <tr>
                                <th>Title:</th>
                                <td>{{ $promotion->title}}</td>
                            </tr>

                            <tr>
                                <th>Message:</th>
                                <td>{!! $promotion->message !!}</td>
                            </tr>
                            <tr>
                                <th>Start Date:</th>
                                <td>{!! \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d') !!}</td>
                            </tr>
                            <tr>
                                <th>End Date:</th>
                                <td>{!! \Carbon\Carbon::parse($promotion->end_date)->format('Y-m-d') !!}</td>
                            </tr>
                            <tr>
                                <th>Apply For:</th>
                                <td>{!! ucwords( $promotion->applied_for) !!}</td>
                            </tr>
                            <tr>
                                <th>Offer Type:</th>
                                <td>{!! ucwords( $promotion->offer_type) !!}</td>
                            </tr>
                            <tr>
                                <th>Offer Value:</th>
                                <td>{!! ucwords( $promotion->offer_value) !!}</td>
                            </tr>
                            <tr>
                                <th>Show on homepage:</th>
                                <td>{!! $promotion->in_homepage == 1 ? 'Yes' : 'No' !!}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>{!! $promotion->status_badge !!}</td>
                            </tr>
                            <tr>
                                <th>Promotion Type:</th>
                                <td>{!! ucwords($promotion->promotion_type) !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <h3>Promotion Items</h3>
            <div class="row">
                @if($promotion->promotion_items->count() > 0)
                    @foreach ($promotion->promotion_items as $item)
                        <div class="col-md-3">
                            <img src="{{$item?->product?->image_url}}" class="img-fluid" width="150" height="150"/>
                            <h5>Name: {{ucwords($item?->product?->name)}}</h5>
                            <p>Category: {{$item?->category?->name}}</p>
                        </div>
                    @endforeach

                @endif

            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{route('promotions.index')}}" class="btn btn-primary">Back</a>
        </div>
    </div>
</div> <!-- end col -->
@endsection

@push('style')
@endpush
@push('script')
@endpush
