@extends('admin.layouts.app')

@section('content')
<div class="col-lg-12 p-0">
    <div class="card">
        <div class="card-body">

            <form action="{{ route('restaurants.store') }}" method="post" enctype="multipart/form-data" data-parsley-validate>
                @csrf
                <input type="hidden" name='type' value="{{\App\Models\User::TYPE_RESTAURANT}}">
                <input type="hidden" name='status' value="{{STATUS_ACTIVE}}">
                <div class="row">
                    <div class="form-group col-xl-4 col-lg-6 p-2">
                        <label>{{ __('Restaurant Name') }} <span class="error">*</span></label>
                        <input type="text" name="first_name" class="form-control" placeholder="Enter Name" required
                            value="{{ old('first_name') }}">

                        @error('first_name')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 p-2">
                        <label>{{ __('User Name') }} <span class="error">*</span></label>
                        <input type="text" name="username" class="form-control" placeholder="Enter User Name" required
                            value="{{ old('username') }}">

                        @error('username')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 p-2">
                        <label>{{ __('Email') }} <span class="error">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="Enter email"
                            value="{{ old('email') }}">

                        @error('email')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 p-2">
                        <label>{{ __('Phone') }}</label>
                        <input type="tel" value="{{ old('phone') ? old('phone') : '+880' }}" name="phone"
                            class="form-control phone">

                        @error('phone')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 p-2">
                        <label>{{ __('Manager Phone') }}</label>
                        <input type="tel" value="{{ old('restaurants.manager_phone') ? old('restaurants.manager_phone') : '+880' }}" name="restaurants[manager_phone]"
                            class="form-control phone">

                        @error('restaurants.manager_phone')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 p-2">
                        <label>{{ __('Password') }} <span class="error">*</span></label>
                        <input type="password" name="password" class="form-control" required
                            placeholder="Enter password">

                        @error('password')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 p-2">
                        <label>{{ __('Confirm Password') }} <span class="error">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required
                            placeholder="Type password again">

                        @error('password_confirmation')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 p-2">
                        <label>{{ __('Image') }}</label>
                        <small>({{ __('Image size max 10MB') }})</small>
                        <div class="ic-form-group position-relative">
                            <input type="file" id="uploadFile" class="f-input form-control" name="avatar" accept="image/*">
                        </div>
                        @error('avatar')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group col-xl-12 col-lg-12 p-2">
                        <label>{{ __('Address') }}</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"  value="{{ old('address')  }}" name="restaurants[address]"></textarea>
                        @error('address')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- <div class="form-group col-xl-4 col-lg-6 p-2">
                     <label class="d-block mb-3 col-md-12">{{ __('Status') }} <span class="error">*</span></label>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="status_yes" value="{{ STATUS_ACTIVE }}"
                                    name="status" class="custom-control-input" checked="">
                                    <label class="form-check-label" for="status_yes">
                                    {{ __('Approve') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="status_no" value="{{ STATUS_INACTIVE }}"
                                        name="status" class="custom-control-input">
                                    <label class="form-check-label"  for="status_no">
                                    {{ __('Reject') }}
                                    </label>
                                </div>
                            </div>

                        </div>


                        @error('status')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div> --}}
                </div>

                <div class="form-group mt-4">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-success waves-effect waves-lightml-2" type="submit">
                            <i class="fa fa-save"></i> {{ __('Submit') }}
                        </button>
                        <a class="btn btn-danger waves-effect" href="{{ route('restaurants.index') }}">
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
