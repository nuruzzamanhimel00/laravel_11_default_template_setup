@extends('admin.layouts.app')
@section('content')
    <div class="col-lg-12 p-0">
        <div class="card">
            <div class="card-body">

                <form action="{{ route('suppliers.update', $user->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name='type' value="{{\App\Models\User::TYPE_SUPPLIER}}">
                    <div class="row">
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Company') }} <span class="error">*</span></label>
                            <input type="text" value="{{ old('company',$user?->supplier->company ?? '')  }}" name="company"
                                class="form-control company" placeholder="Enter Company" required >

                            @error('company')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Email') }} <span class="error">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="Enter email"
                                value="{{ old('email',$user->email) }}">

                            @error('email')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Phone') }}</label>
                            <input type="tel" value="{{ old('phone') ? old('phone') : $user->phone}}" name="phone"
                                class="form-control phone">

                            @error('phone')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Name') }} <span class="error">*</span></label>
                            <input type="text" name="first_name" class="form-control" placeholder="Enter Name" required
                                value="{{ old('first_name',$user->first_name) }}">

                            @error('first_name')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>



                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Description') }}</label>
                            <input type="text" value="{{ old('designation',$user?->supplier->designation ?? '')  }}" name="designation"
                                class="form-control designation">

                            @error('designation')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Address') }}</label>
                            <input type="text" value="{{ old('address',$user?->supplier->address ?? '')  }}" name="address"
                                class="form-control address">

                            @error('address')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        {{-- <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Country') }}</label>
                            <input type="text" value="{{ old('country',$user?->supplier->country ?? "")  }}" name="country"
                                class="form-control country">

                            @error('country')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div> --}}
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('City') }}</label>
                            <input type="text" value="{{ old('city',$user?->supplier->city ?? "")  }}" name="city"
                                class="form-control city">

                            @error('city')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Zip Code') }}</label>
                            <input type="text" value="{{ old('zipcode',$user?->supplier->zipcode ?? '')  }}" name="zipcode"
                                class="form-control zipcode">

                            @error('zipcode')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-xl-12 col-lg-12 p-2">
                            <label for="short_address">Short address (if you are not fill up this above address then you can fill this short address)</label>
                            <textarea class="form-control"  rows="3" id="short_address" name="short_address">{{$user?->supplier->short_address ?? ''}}</textarea>

                            @error('short_address')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="form-group col-xl-4 col-lg-6 p-2">
                            <label>{{ __('Profile Image') }}</label>
                            <small>({{ __('Image size max 10mb') }})</small>
                            <div class="row">
                                <div class="col-lg-8 col-md-12 col">
                                    <div class="ic-form-group position-relative">
                                        <input type="file" id="uploadFile" class="f-input form-control image_pick" data-image-for="avatar" name="avatar" accept="image/*">
                                    </div>
                                </div>
                                @if(!is_null($user->avatar))
                                <div class="col-lg-4 col-md-6">
                                    <a href="{{ $user->avatar_url }}" target="_blank">
                                        <img class="img-64 mt-0 mt-md-0" src="{{ $user->avatar_url }}" id="img_avatar" alt="avatar" />
                                    </a>
                                </div>
                                @endif
                            </div>
                            @error('avatar')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="form-group col-xl-4 col-lg-6 p-2">
                            <div class="row">
                                <label class="d-block mb-3 col-md-12">{{ __('Status') }} <span class="error">*</span></label>
                                <div class="custom-control custom-radio custom-control-inline col-md-3">
                                    <input type="radio" id="status_yes" value="{{ STATUS_ACTIVE }}"
                                           name="status" class="custom-control-input" {{$user->status == STATUS_ACTIVE ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="status_yes">{{ __('Active') }}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline col-md-4">
                                    <input type="radio" id="status_no" value="{{ STATUS_INACTIVE }}"
                                           name="status" class="custom-control-input" {{$user->status == STATUS_INACTIVE ? 'checked' : ''}} >
                                    <label class="custom-control-label" for="status_no">{{ __('Inactive') }}</label>
                                </div>
                            </div>


                            @error('status')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <br>
                    <div class="form-group">
                        <div>
                            <button class="btn btn-success waves-effect waves-lightml-2" type="submit">
                                <i class="fa fa-save"></i> {{ __('Submit') }}
                            </button>
                            <a class="btn btn-danger waves-effect" href="{{ route('suppliers.index') }}">
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
