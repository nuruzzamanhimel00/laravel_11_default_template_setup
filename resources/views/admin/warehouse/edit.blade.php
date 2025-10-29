@extends('admin.layouts.app')

@section('content')
<div class="col-lg-12 p-0">
    <div class="card">
        <div class="card-body">

            <x-form :action="route('warehouses.update', $warehouse->id)" method="PUT">
                <div class="row">
                    <x-form-group
                        name="name"
                        value="{{ old('name', $warehouse?->name) }}"
                        label="{{ __('Warehouse name') }}"
                        placeholder="Warehouse Name"
                        groupClass="form-group col-xl-4 col-lg-4 p-2"
                        required
                    />
                    <x-form-group
                        name="email"
                        value="{{ old('email', $warehouse?->email) }}"
                        label="{{ __('Email') }}"
                        placeholder="Email"
                        type="email"
                        groupClass="form-group col-xl-4 col-lg-4 p-2"
                    />
                    <div class="form-group col-xl-4 col-lg-4 p-2">
                        <x-label :is_required="true">{{ __('Phone') }}</x-label>
                        <x-country-phone-input name="phone" value="{{ old('phone', $warehouse?->phone) }}" />
                        @error('phone')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-form-group
                        name="company_name"
                        value="{{ old('company_name', $warehouse?->company_name) }}"
                        label="{{ __('Company name') }}"
                        placeholder="Company Name"
                        groupClass="form-group col-xl-4 col-lg-4 p-2"
                    />
                    <x-form-group
                        name="address"
                        value="{{ old('address', $warehouse?->address) }}"
                        label="{{ __('Address') }}"
                        placeholder="Address"
                        groupClass="form-group col-xl-4 col-lg-4 p-2"
                    />
                </div>
                <div class="row">
                    <x-checkbox-form
                        name="is_default"
                        value="1"
                        checked="{{ old('is_default', $warehouse?->is_default) }}"
                        label="{{ __('Is Default Warehouse') }}"
                        placeholder="Is Default Warehouse"
                        groupClass="col-xl-4 col-lg-4 p-2 mt-4"
                    />

                    <x-status-group value="{{ old('status', $warehouse?->status) }}" main_div_class="col-xl-4 col-lg-4 p-2" />
                </div>
                <br>
                <x-form-submit-buttons cancel_url="{{ route('warehouses.index') }}" />
            </x-form>
        </div>
    </div>
</div> <!-- end col -->
@endsection

@push('style')
@endpush
@push('script')
@endpush
