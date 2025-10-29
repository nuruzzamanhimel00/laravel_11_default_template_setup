@extends('admin.layouts.app')

@section('content')
    <div class="col-lg-12 p-0" id="vueApp">
        <div class="card">
            <div class="card-body">

                <form action="{{ route('attributes.update', $attribute->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <h4 class="header-title">{{ __('Edit Attribute') }}</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Attribute Name') }} <span class="error">*</span></label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Enter Attribute Name" value="{{ $attribute->name ?? old('name') }}" required >

                                @error('name')
                                    <p class="error">{{ $message }}</p>
                                @enderror

                            </div>
                        </div>


                    </div>
                    <div>
                        <attribute-items :attribute-items-data="{{ json_encode($attribute->values) }}" />
                    </div>
                    <div class="row">
                        <div class="form-group col-xl-4 col-lg-6 p-2">
                            <label class="d-block mb-3 col-md-12">{{ __('Status') }} <span class="error">*</span></label>
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" id="status_yes"
                                            value="{{ STATUS_ACTIVE }}" name="status"
                                            class="custom-control-input"
                                            {{
                                                $attribute->status == STATUS_ACTIVE ? 'checked' : ''
                                            }}
                                            >
                                        <label class="form-check-label" for="status_yes">
                                            {{ __('Active') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" id="status_no"
                                            value="{{ STATUS_INACTIVE }}" name="status"
                                            {{
                                                $attribute->status == STATUS_INACTIVE ? 'checked' : ''
                                            }}
                                            class="custom-control-input">
                                        <label class="form-check-label" for="status_no">
                                            {{ __('Inactive') }}
                                        </label>
                                    </div>
                                </div>

                            </div>


                            @error('status')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-success waves-effect waves-lightml-2" type="submit">
                                <i class="fa fa-save"></i> {{ __('Submit') }}
                            </button>
                            <a class="btn btn-danger waves-effect" href="{{ route('attributes.index') }}">
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
