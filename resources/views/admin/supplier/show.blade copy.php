@extends('admin.layouts.app')
@section('content')
    <div class="col-lg-12 p-0">
        <div class="card">
            <div class="card-body">
                <div class="emp-profile ic-employe-warper-container">
                    <div class="ic-customer-details-warper">
                        <div class="ic-customer-profile-basic-info supplier">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="profile-img">
                                        <img src="{{ $user->avatar_url }}" alt="Germane Aaron" class="img-thumbnail">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="ic-customer-basic-info">
                                        <h5 class="text-muted">Basic info</h5>
                                        <div class="profile-head">
                                            <h5>{{$user->name}}</h5>
                                            <h6>{{$user->email}}</h6>
                                            <p class="ic-discription-customer mb-0">{{$user->phone}}</p>
                                            <p class="ic-discription-customer mb-0">Company: {{$user?->supplier->company ?? ''}}</p>
                                            <p class="ic-discription-customer mb-0">Designation: {{$user?->supplier->designation ?? ''}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="ic-customer-adress-info">
                                        <div class="profile-head">
                                            <h5 class="text-muted">Address</h5>
                                            <address class="ic-address-info-customer">
                                                {{$user?->supplier->address ?? ''}}, <br>
                                                {{$user?->supplier->country ?? ''}}, <br>
                                                {{$user?->supplier->city ?? ''}}, <br>
                                                {{$user?->supplier->zipcode ?? ''}}, <br>

                                            </address>
                                            <address class="ic-address-info-customer">
                                                Short address:     {{$user?->supplier->short_address ?? ''}},
                                            </address>
                                            <h6 title="Status">
                                                @php
                                                    $badge = $user->status == \App\Models\User::STATUS_ACTIVE ? "bg-success" : "bg-danger";
                                                    $statusBadge = '<span class="badge ' . $badge . '">' . Str::upper($user->status) . '</span>';
                                                @endphp

                                                {!! $statusBadge !!}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="ic-profile-details-goback">
                                        <a href="{{route('suppliers.index')}}" class="btn btn-success float-end">
                                            <i class="bi bi-arrow-left"></i> Back
                                        </a>
                                    </div>
                                </div>
                            </div>



                        </div>

                    </div>
                    {{-- <section id="tabs" class="project-tab">
                        <div class="ic-employe-warper-container">
                            <div class="row">
                                <div class="col-md-12 p-0">
                                    <nav class="ic-customer-details-tab">
                                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Purchase History</button>
                                            <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Product History</button>
                                        </div>
                                    </nav>
                                    <div class="tab-content" id="nav-tabContent">
                                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h6 class="text-muted text-center">Purchase History</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Purchase Number</th>
                                                                    <th>Date</th>
                                                                    <th>Total</th>
                                                                    <th>Total Product</th>
                                                                    <th>Status</th>
                                                                    <th>Received</th>
                                                                    <th>Missing Item</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <a target="_blank" href="https://clanvent-alpha.laravel-script.com/admin/purchases/4" class="btn btn-link">11222253</a>
                                                                    </td>
                                                                    <td>
                                                                        April 04, 2024 <br>
                                                                        <small>01:22:25 AM</small>
                                                                    </td>
                                                                    <td>$ 2513080.00</td>
                                                                    <td>1</td>
                                                                    <td>
                                                                        <span class="badge bg-success">REQUESTED</span>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge bg-warning">Not Received Yet</span>
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <a target="_blank" href="https://clanvent-alpha.laravel-script.com/admin/purchases/1" class="btn btn-link">11614343</a>
                                                                    </td>
                                                                    <td>
                                                                        December 12, 2022 <br>
                                                                        <small>16:14:34 PM</small>
                                                                    </td>
                                                                    <td>$ 200000.00</td>
                                                                    <td>1</td>
                                                                    <td>
                                                                        <span class="badge bg-primary">CONFIRMED</span>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge bg-success">Received</span>
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h6 class="text-muted text-center">Product History</h6>
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Product Id</th>
                                                                <th>Product Name</th>
                                                                <th>SKU</th>
                                                                <th>Price</th>
                                                                <th>Quantity</th>
                                                                <th>Sub Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <a target="_blank" href="https://clanvent-alpha.laravel-script.com/admin/products/50/edit" class="btn btn-link">00000050</a>
                                                                </td>
                                                                <td>Polo shirt</td>
                                                                <td>Polo-shirt-122</td>
                                                                <td>125654</td>
                                                                <td>20</td>
                                                                <td>$ 2513080</td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <a target="_blank" href="https://clanvent-alpha.laravel-script.com/admin/products/47/edit" class="btn btn-link">00000047</a>
                                                                </td>
                                                                <td>iPhone 13 Pro</td>
                                                                <td>i-01003</td>
                                                                <td>1300</td>
                                                                <td>200</td>
                                                                <td>$ 260000</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section> --}}
                </div>
            </div>
        </div>
    </div> <!-- end col -->
@endsection


@push('style')
@endpush

@push('script')
@endpush
