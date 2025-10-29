@extends('admin.layouts.app')

@section('content')
    <div class="col-lg-12 p-0">
        <div class="card">
            <div class="card-body">

                <form action="{{ route('purchases.receive.store', $id) }}" method="post" enctype="multipart/form-data"
                    data-parsley-validate>
                    @csrf
                    <input name="warehouse_id" type="hidden" value="{{ $purchase->warehouse_id }}" />

                    <div class="row py-2">
                        <div class="col-lg-4">
                            <div class="form-group"><label for="date" class="pt-2">Receive Date <span
                                        class="error">*</span></label> <input type="date" name="receive_date" id="receive_date"
                                    value="{{date('Y-m-d')}}" required="required" placeholder="Date"
                                    class="form-control "></div>
                        </div>
                    </div>
                    <div class="table-responsive py-2">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td><b>Purchase Number : </b></td>
                                                    {{-- <td>:</td> --}}
                                                    <td>{{ $purchase->purchase_number }}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Company :</b></td>
                                                    {{-- <td>:</td> --}}
                                                    <td>{{$purchase->supplier?->supplier?->company}} </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Supplier Phone :</b></td>
                                                    {{-- <td>:</td> --}}
                                                    <td>{{$purchase->supplier?->phone}}</td>
                                                </tr>


                                                <tr>
                                                    <td><b>Warehouse :</b></td>
                                                    {{-- <td>:</td> --}}
                                                    <td>{{$purchase->warehouse?->name}}</td>
                                                </tr>
                                                {{-- <tr>
                                                    <td><b>Company</b></td>
                                                    <td>:</td>
                                                    <td>{{$purchase->company ?? ''}}</td>
                                                </tr> --}}

                                                {{-- <tr>
                                                    <td><b> Short address </b></td>
                                                    <td>:</td>
                                                    <td>{{$purchase->short_address}}</td>
                                                </tr> --}}




                                            </tbody>
                                        </table>
                                    </td>
                                    <td>
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td><b> Date :</b></td>
                                                    {{-- <td>:</td> --}}
                                                    <td>{{$purchase->date}}</td>
                                                </tr>
                                                <tr>
                                                    <td><b> Note :</b></td>
                                                    {{-- <td>:</td> --}}
                                                    <td>{{$purchase->notes}}</td>
                                                </tr>
                                                {{-- <tr>
                                                    <td><b>Address</b></td>
                                                    <td>:</td>
                                                    <td>{{$purchase->address}}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>City </b></td>
                                                    <td>:</td>
                                                    <td>{{$purchase->city}} </td>
                                                </tr>
                                                <tr>
                                                    <td><b>zipcode</b></td>
                                                    <td>:</td>
                                                    <td>{{$purchase->zipcode}}</td>
                                                </tr> --}}

                                                <tr>
                                                    <td><b>Status :</b></td>
                                                    {{-- <td>:</td> --}}
                                                    <td><span class="badge
                                                        {{
                                                        $purchase->status == App\Models\Purchase::STATUS_REQUESTED ? "bg-info" : ( $purchase->status == App\Models\Purchase::STATUS_CANCEL ? "bg-danger": "bg-success")
                                                        }}
                                                        ">{{ ucwords($purchase->status) }}</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row py-2">
                        <div class="col-sm-12" id="vueApp">
                            <purchase-receive-list :purchase-items="{{ json_encode($purchaseItems) }}" :currency="{{json_encode(getCurrency())}}" />
                        </div>
                    </div>
                    <div class="form-group py-2">
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-success waves-effect waves-lightml-2" type="submit">
                                <i class="fa fa-save"></i> {{ __('Submit') }}
                            </button>
                            <a class="btn btn-danger waves-effect" href="{{ route('purchases.index') }}">
                                <i class="fa fa-times"></i> {{ __('Cancel') }}
                            </a>
                        </div>
                    </div>




                </form>
            </div>
        </div>
    </div> <!-- end col -->
@endsection

@push('style')
@endpush
@push('script')
    <script>
        $(document).ready(function() {

        });
    </script>
@endpush
