@extends('admin.layouts.app')

@section('content')
    <div class="col-lg-12 p-0">
        <div class="card">
            <div class="card-body">

                <form action="{{ route('purchases.update', $purchase->id) }}" method="post" enctype="multipart/form-data"
                    data-parsley-validate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name='warehouse_id'  value="{{$purchase->warehouse_id}}">

                    <div class="row">
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Select Warehouses') }} <span class="error">*</span></label>
                            <div>
                                @if ($warehouses)
                                    <select name="warehouse_id" class="form-control select2" required id="warehouse_id" disabled readonly>
                                        <option value="" selected disabled>{{ __('Select Warehouse') }}</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value=" {{ $warehouse->id }}"
                                                {{$purchase->warehouse_id == $warehouse->id  ? 'selected' : '' }}
                                                >{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            @error('warehouse_id')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Company') }} <span class="error">*</span></label>
                            <div>
                                @if ($suppliers)
                                    <select name="supplier_id" class="form-control select2" required id="supplier_id">
                                        <option value="" selected disabled>{{ __('Select Company') }}</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value=" {{ $supplier->id }}"
                                                {{$purchase->supplier_id == $supplier->id  ? 'selected' : '' }}
                                                >{{ $supplier->first_name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            @error('supplier_id')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>



                        {{-- <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Company') }} </label>
                            <input type="text" name="company" class="form-control"
                                  id="company" value="{{ old('company', $purchase->company)  }}">
                            @error('company')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div> --}}

                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Date') }} <span class="error">*</span></label>
                            <input type="date" name="date" class="form-control"
                                required  id="date" value="{{ old('date',$purchase->date)}}">

                            @error('date')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        {{-- <div class="col-md-12">
                            <h5>{{ __('Address') }}</h5>
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Address') }}</label>
                            <input type="text" value="{{ old('address', $purchase->address)  }}" name="address"
                                class="form-control address">

                            @error('address')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Country') }}</label>
                            <input type="text" value="{{ old('country', $purchase->country)  }}" name="country"
                                class="form-control country">

                            @error('country')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('City') }}</label>
                            <input type="text" value="{{ old('city', $purchase->city)  }}" name="city"
                                class="form-control city">

                            @error('city')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Zip Code') }}</label>
                            <input type="text" value="{{ old('zipcode', $purchase->zipcode)  }}" name="zipcode"
                                class="form-control zipcode">

                            @error('zipcode')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-xl-12 col-lg-12 p-2">
                            <label for="short_address">Short address (if you are not fill up this above address then you can fill this short address)</label>
                            <textarea class="form-control"  rows="5" id="short_address" name="short_address">{{$purchase->short_address}}</textarea>

                            @error('short_address')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div> --}}
                        <div class="form-group col-xl-12 col-lg-12 p-2">
                            <label for="notes">{{ __('Notes') }} </label>
                            <div>

                                <textarea class="form-control" id="notes" rows="5" name="notes">{{$purchase->notes}}</textarea>

                            </div>
                            @error('delivery_type')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Purchase Search List --->
                    <div id="vueApp">
                        <purchase-search-list :purchase-items="{{json_encode($purchase_items)}}" />
                    </div>


                    <div class="form-group mt-4">
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-success waves-effect waves-lightml-2" type="submit">
                                <i class="fa fa-save"></i> {{ __('Submit') }}
                            </button>
                            <a class="btn btn-danger waves-effect" href="{{ route('purchases.index') }}">
                                <i class="fa fa-times"></i> {{ __('Back') }}
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

    $(document).ready(function () {
        let suppliers = @json($suppliers);
        $(document).on('change','#supplier_id', function(){
            let supplier_id = $(this).val();
            let supplier = suppliers.find(supplier => supplier.id == supplier_id);

            if(supplier){
                $('#address_line_1').val(supplier.address);
                $('#address_line_2').val(supplier.address);

            }
        })

        $('#order_date').on('change', function () {
            const orderDate = $(this).val(); // Get selected order date
            let defaultData = "{{ date('Y-m-d') }}";
            if (orderDate) {
                $('#delivery_date').attr('min', orderDate); // Set the min attribute for delivery date
            } else {
                $('#delivery_date').attr('min', defaultData); // Remove min if order date is cleared
            }
        });

        // $(document).on('change','#warehouse_id', function(e){
        //     let value = $(this).val();
        //     window.location.href= "{{route('purchases.create')}}?warehouse_id="+value;
        // })
    });
</script>
@endpush
