@extends('admin.layouts.app')

@section('content')
<div class="col-lg-8 p-0">
    <div class="card">
        <div class="card-body">

            <form action="{{ route('categories.update', $data->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate>
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="form-group col-xl-6 col-lg-6 p-2">
                        <label>Parent Category </label>
                        <div>
                            <select name="parent_id" class="form-control">
                                <option value="">Select Parent Category </option>
                                @foreach ($patent_categories as $category )
                                    <option value="{{$category->id}}" {{$category->id == $data->parent_id ? 'selected' : ''}}>{{$category->name}}</option>
                                @endforeach

                            </select>
                            @error('parent_id')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group col-xl-6 col-lg-6 p-2">
                        <label>{{ __(' Category Name') }} <span class="error">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder=" Name" required
                            value="{{ $data->name ?? old('name') }}">
                        @error('name')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group col-xl-6 col-lg-6 p-2">
                        <label>{{ __('Description') }}</label>
                        <textarea  name="description" class="form-control" placeholder=" Description"
                            >{{ $data->description  }}</textarea>
                        @error('description')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group col-xl-6 col-lg-6 p-2">
                        <label>Image</label>
                        <small>Supported type: png, jpg, jpeg | Max size: 100MB | <br> (73 * 70) px</small>
                        <div class="ic-form-group position-relative">
                            <input type="file" id="uploadFile" class="form-control" name="image" accept="image/*">
                            @if(!is_null($data->image))
                            <a href="{{ ($data->image_url )}}" target="_blank">
                            <img class="img-64 mt-0 mt-md-0" src="{{ $data->image_url }}" id="img_avatar" alt="avatar" />
                            </a>
                            @endif
                        </div>
                    </div>


                    <div class="form-group col-xl-6 col-lg-6 p-2">
                        <div class="row">
                            <label class="d-block mb-3 col-md-12">{{ __('Status') }} <span class="error">*</span></label>
                            <div class="custom-control custom-radio custom-control-inline col-md-3">
                                <input type="radio" id="status_yes" value="{{ STATUS_ACTIVE }}"
                                       name="status" class="custom-control-input" {{$data->status == STATUS_ACTIVE ? 'checked' : ''}}>
                                <label class="custom-control-label" for="status_yes">{{ __('Active') }}</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline col-md-4">
                                <input type="radio" id="status_no" value="{{ STATUS_INACTIVE }}"
                                       name="status" class="custom-control-input" {{$data->status == STATUS_INACTIVE ? 'checked' : ''}}>
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
                            <i class="fa fa-save"></i> {{ __('Update') }}
                        </button>
                        <a class="btn btn-danger waves-effect" href="{{ route('categories.index') }}">
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
