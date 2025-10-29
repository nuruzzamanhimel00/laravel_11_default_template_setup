@extends('admin.layouts.app')
@section('content')
    <div class="col-lg-12 p-0">
        <div class="card">
            <div class="card-body">
                {{-- <h4 class="header-title">{{ __('Edit Promotion') }}</h4> --}}
                <form action="{{ route('promotions.update', $promotion->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @php
                        $date = date('Y-m-d'); // Current date in 'Y-m-d' format
                    @endphp
                    <div class="row" x-data="{
                        start_date: '{{ old('start_date', $promotion->start_date) }}',
                        end_date: '{{ old('end_date', $promotion->end_date) }}',
                        min_date: '{{ $promotion->start_date }}',
                    }">
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('User Type') }} <span class="error">*</span></label>
                            <select class="form-control" name="target_type" required>
                                <option selected>Select Type</option>
                                <option value="{{ \App\Models\User::TYPE_RESTAURANT }}"
                                {{ old('target_type', $promotion->target_type) == \App\Models\User::TYPE_RESTAURANT ? 'selected' : '' }}>
                                    {{ ucwords(\App\Models\User::TYPE_RESTAURANT) }}</option>
                                <option value="{{ \App\Models\User::TYPE_REGULAR_USER }}"
                                {{ old('target_type', $promotion->target_type) == \App\Models\User::TYPE_REGULAR_USER ? 'selected' : '' }}>
                                    {{ ucwords(\App\Models\User::TYPE_REGULAR_USER) }}</option>
                            </select>

                            @error('target_type')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Title') }} <span class="error">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Enter Title" required
                                value="{{ old('title', $promotion->title) }}">

                            @error('title')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group col-xl-12 col-lg-12 p-2">
                            <label>{{ __('Message') }} <span class="error">*</span></label>

                            <textarea name="message" class="form-control" id="summernote" rows="3" placeholder="Enter meta description">{{old('message', $promotion->message)}}</textarea>

                            @error('message')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Start Date') }} <span class="error">*</span></label>
                            <input type="date" name="start_date" class="form-control" x-model="start_date"
                                @input="
                                    end_date = '';
                                    min_date = $event.target.value;
                                "
                                required placeholder="Enter Start Date">

                            @error('start_date')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('End Date') }} <span class="error">*</span></label>
                            <input type="date" name="end_date" class="form-control" :min="min_date"
                                x-model="end_date" required placeholder="Enter End Date">

                            @error('end_date')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>



                        <div class="form-group col-xl-4 col-lg-6 p-2">
                            <label>{{ __('Image') }}</label>
                            <small>(1200 x 628 pixels)</small>
                            <div class="ic-form-group position-relative">
                                <input type="file" id="uploadFile" class="f-input form-control" name="image">
                                @if(!is_null($promotion->image))
                                <a href="{{ ($promotion->image_url )}}" target="_blank">
                                <img class="img-64 mt-0 mt-md-0" src="{{ $promotion->image_url }}" id="img_avatar" alt="avatar" />
                                </a>
                                @endif
                            </div>
                            @error('image')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="form-group col-xl-4 col-lg-6 p-2">
                            <div class="row">
                                <label class="d-block mb-3 col-md-12">{{ __('Status') }} <span
                                        class="error">*</span></label>
                                <div class="custom-control custom-radio custom-control-inline col-md-3">
                                    <input type="radio" id="status_yes" value="{{ STATUS_ACTIVE }}" name="status"
                                        class="custom-control-input" {{ old('status', $promotion->status) == STATUS_ACTIVE ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="status_yes">{{ __('Active') }}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline col-md-4">
                                    <input type="radio" id="status_no" value="{{ STATUS_INACTIVE }}" name="status"
                                        class="custom-control-input" {{ old('status', $promotion->status) == STATUS_INACTIVE ? 'checked' : ''}} >
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
                            <a class="btn btn-danger waves-effect" href="{{ route('promotions.index') }}">
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
{{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
<script>
    $(document).ready(function() {
        if ($("#summernote").length > 0) {
            $('#summernote').summernote();
        }
    });
  </script>
@endpush
