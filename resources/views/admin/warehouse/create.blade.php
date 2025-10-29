@extends('admin.layouts.app')

@section('content')
    <div class="col-lg-12 p-0">
        <div class="card">
            <div class="card-body">

                <x-form :action="route('warehouses.store')">
                    <div class="row">
                        <x-form-group name="name" label="{{ __('Warehouse name') }}" placeholder="Warehouse Name"
                            groupClass="form-group col-xl-4 col-lg-4 p-2" required />
                        <x-form-group name="email" label="{{ __('Email') }}" placeholder="Email" type="email"
                            groupClass="form-group col-xl-4 col-lg-4 p-2" />
                        <div class="form-group col-xl-4 col-lg-4 p-2">
                            <x-label :is_required="true">{{ __('Phone') }}</x-label>
                            <x-country-phone-input name="phone" />
                            @error('phone')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <x-form-group name="company_name" label="{{ __('Company name') }}" placeholder="Company Name"
                            groupClass="form-group col-xl-4 col-lg-4 p-2" />
                        <x-form-group name="address" label="{{ __('Address') }}" placeholder="Address"
                            groupClass="form-group col-xl-4 col-lg-4 p-2" />
                    </div>
                    <div class="row *:">
                        <div class="col-xl-4 col-lg-4 p-2 mt-4">
                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input mr-2" value="1" placeholder="" type="checkbox"
                                    name="is_default" id="is_default_checkbox">

                                <label class="block font-medium text-sm text-gray-700 form-check-label mb-0 ms-2"
                                    for="is_default_checkbox">
                                    Is Default Warehouse

                                </label>
                                @error('is_default')
                                <p class="error">{{ $message }}</p>
                            @enderror
                            </div>

                        </div>

                        <x-status-group value="{{ old('status', STATUS_ACTIVE) }}" main_div_class="col-xl-4 col-lg-4 p-2" />
                    </div>
                    <br/>
                    <x-form-submit-buttons cancel_url="{{ route('warehouses.index') }}" />


                </x-form>
            </div>
        </div> <!-- end col -->
    @endsection

    @push('style')
    @endpush
    @push('script')
    @endpush
