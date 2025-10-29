@extends('admin.layouts.app')

@section('content')
<div class="col-lg-6 p-0">
    <div class="card">
        <div class="card-body">
            {{-- <h4 class="header-title">{{ __('Add Brand') }}</h4> --}}
            <form action="{{ route('brands.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="form-group col-xl-12 col-lg-12 p-2">
                        <label>{{ __('Brand name') }} <span class="error">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Brand Name" required
                            value="{{ old('name') }}">

                        @error('name')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group col-xl-12 col-lg-12 p-2">
                        <label>{{ __('Image') }}</label>
                        <small>({{ __('Image size max 10MB') }})</small>
                        <div class="ic-form-group position-relative">
                            <input type="file" id="uploadFile" class="f-input form-control" name="image"
                                accept="image/*">
                        </div>
                        @error('image')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group col-xl-12 col-lg-12 p-2">
                        <div class="row">
                            <label class="d-block mb-3 col-md-12">{{ __('Status') }} <span
                                    class="error">*</span></label>
                            <div class="custom-control custom-radio custom-control-inline col-md-3">
                                <input type="radio" id="status_yes" value="{{ \App\Models\User::STATUS_ACTIVE }}"
                                    name="status" class="custom-control-input form-check-input" checked="">
                                <label class="custom-control-label" for="status_yes">{{ __('Active') }}</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline col-md-4">
                                <input type="radio" id="status_no" value="{{ \App\Models\User::STATUS_INACTIVE }}"
                                    name="status" class="custom-control-input form-check-input">
                                <label class="custom-control-label" for="status_no">{{ __('Inactive') }}</label>
                            </div>
                        </div>


                        @error('status')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div>
                        <button class="btn btn-success waves-effect waves-lightml-2" type="submit">
                            <i class="fa fa-save"></i> {{ __('Submit') }}
                        </button>
                        <a class="btn btn-danger waves-effect" href="{{ route('brands.index') }}">
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
@endpush