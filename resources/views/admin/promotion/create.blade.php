@extends('admin.layouts.app')

@section('content')
<div class="col-lg-12 p-0">
    @php
    $date = date('Y-m-d'); // Current date in 'Y-m-d' format

    @endphp
    <div class="card" x-data="promotionData()">
        <div class="card-body">
            {{-- <h4 class="header-title">{{ __('Add Promotion') }}</h4> --}}
            <form action="{{ route('promotions.store') }}" method="post" enctype="multipart/form-data">
                @csrf


                <div class="row">
                    <div class="form-group col-xl-6 col-lg-6 p-2">
                        <label>{{ __('User Type') }} <span class="error">*</span></label>
                        <select class="form-control" name="target_type" x-model="target_type" id="target_type" required>
                            <option selected>Select Type</option>
                            <option value="{{\App\Models\User::TYPE_REGULAR_USER}}"
                                {{$target_type == \App\Models\User::TYPE_REGULAR_USER ? 'selected' : ''}}>
                                {{ucwords(\App\Models\User::TYPE_REGULAR_USER)}}</option>
                            <option value="{{\App\Models\User::TYPE_RESTAURANT}}"
                                {{$target_type == \App\Models\User::TYPE_RESTAURANT ? 'selected' : ''}}>
                                {{ucwords(\App\Models\User::TYPE_RESTAURANT)}}</option>
                        </select>

                        @error('target_type')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group col-xl-6 col-lg-6 p-2">
                        <label>{{ __('Title') }} <span class="error">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Enter Title" required
                            value="{{ old('title') }}">

                        @error('title')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group col-xl-6 col-lg-6 p-2">
                        <label>{{ __('Start Date') }} <span class="error">*</span></label>
                        <input type="date" name="start_date" class="form-control" x-model="start_date" @input="
                            end_date = '';
                            is_load_data = false;
                            min_date = $event.target.value;
                        " required placeholder="Enter Start Date">

                        @error('start_date')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group col-xl-6 col-lg-6 p-2">
                        <label>{{ __('End Date') }} <span class="error">*</span></label>
                        <input type="date" name="end_date" class="form-control" :min="min_date" x-model="end_date"
                            required placeholder="Enter End Date" @change="loadData">

                        @error('end_date')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <template x-if=" is_load_data == true">
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Apply For') }} <span class="error">*</span></label>
                            <select class="form-control" x-model="applied_for" name="applied_for"
                                @change="select2Initial()" required>
                                <option selected>Select One</option>
                                @foreach (\App\Models\Promotion::APPLICABLE_TYPES as $applicable)

                                <option value="{{$applicable}}" {{old('applied_for') == $applicable ? 'selected' : ''}}>
                                    {{ucwords($applicable)}}</option>
                                @endforeach

                            </select>

                            @error('applied_for')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                    </template>

                    <template
                        x-if="applied_for == '{{\App\Models\Promotion::APPLICABLE_PRODUCTS}}' && is_load_data == true">
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Products') }} <span class="error">*</span></label>
                            {{-- <select
                                class="form-control select2"
                                name="applied_for_ids[]"
                                multiple="multiple" required>

                                @foreach ($products as $product)
                                    <option
                                        value="{{ $product->id }}"
                            {{ in_array($product->id, old('applied_for_ids', [])) ? 'selected' : '' }}
                            >
                            {{ ucwords($product->name) }}
                            </option>
                            @endforeach
                            </select> --}}
                            <select class="form-control select2" name="applied_for_ids[]" multiple="multiple" required
                                x-data="{ products: products, applied_for_ids: applied_for_ids}">
                                <template x-for="product in products" :key="product.id">
                                    <option :value="product.id" :selected="applied_for_ids.includes(product.id)"
                                        x-text="product.name">
                                    </option>
                                </template>
                            </select>

                            @error('applied_for_ids')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </template>
                    <template
                        x-if="applied_for == '{{\App\Models\Promotion::APPLICABLE_CATEGORIES}}' && is_load_data == true">
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label>{{ __('Categories') }} <span class="error">*</span></label>
                            <select class="form-control select2" name="applied_for_ids[]" multiple="multiple" required
                                x-data="{ categories: categories, applied_for_ids: applied_for_ids}">
                                <template x-for="category in categories" :key="category.id">
                                    <option :value="category.id" :selected="applied_for_ids.includes(category.id)"
                                        x-text="category.name">
                                    </option>
                                </template>
                            </select>

                            <small class="text-danger">Products already used in this promotion are not eligible for
                                reapplication.</small>

                            @error('applied_for_ids')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </template>



                    <div class="form-group col-xl-12 col-lg-12 p-2">
                        <label>{{ __('Message') }} <span class="error">*</span></label>

                        <textarea name="message" class="form-control" id="summernote" rows="3"
                            placeholder="Enter meta description">{{old('message')}}</textarea>

                        @error('message')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>


                    <div class="form-group col-xl-6 col-lg-6 p-2">
                        <label>{{ __('Offer Type') }} <span class="error">*</span></label>
                        <select class="form-control" name="offer_type" required>
                            <option selected>Select Type</option>
                            @foreach (\App\Models\Promotion::OFFER_TYPES as $type)
                            <option value="{{$type}}" {{old('offer_type') == $type ? 'selected' : '' }}>
                                {{ucwords($type)}}</option>
                            @endforeach

                        </select>

                        @error('offer_type')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group col-xl-6 col-lg-6 p-2">
                        <label>{{ __('Offer Value') }} <span class="error">*</span></label>
                        <input type="number"  min="1" name="offer_value" class="form-control"
                            placeholder="Enter Offer Value" required value="{{ old('offer_value') }}">

                        @error('offer_value')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>





                    <div class="form-group col-xl-4 col-lg-4 p-2">
                        <label>{{ __('Image') }}</label>
                        <small>(1200 x 628 pixels)</small>
                        <div class="ic-form-group position-relative">
                            <input type="file" id="uploadFile" class="f-input form-control" name="image">
                        </div>
                        @error('image')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>


                    <div class="form-group col-xl-4 col-lg-4 p-2">
                        <label>{{ __('Show on Homepage') }}</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="in_homepage" name="in_homepage"
                                value="1" checked>
                            <label class="form-check-label" for="in_homepage">
                                {{-- {{ old('in_homepage') ? 'Enabled' : 'Disabled' }} --}}
                            </label>
                        </div>
                        @error('in_homepage')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group col-xl-4 col-lg-4 p-2">
                        <div class="row">
                            <label class="d-block mb-3 col-md-12">{{ __('Status') }} <span
                                    class="error">*</span></label>
                            <div class="custom-control custom-radio custom-control-inline col-md-3">
                                <input type="radio" id="status_yes" value="{{ STATUS_ACTIVE }}" name="status"
                                    class="custom-control-input form-check-input" checked="">
                                <label class="custom-control-label" for="status_yes">{{ __('Active') }}</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline col-md-4">
                                <input type="radio" id="status_no" value="{{ STATUS_INACTIVE }}" name="status"
                                    class="custom-control-input form-check-input">
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
    // $(document).on('change','#target_type', function(e){
    //     let value = $(this).val();
    //     window.location.href= "{{route('promotions.create')}}?target_type="+value;
    // })
});
</script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('promotionData', () => ({

        start_date: "{{ old('start_date', date('Y-m-d')) }}",
        end_date: "",
        min_date: "{{ date('Y-m-d') }}",
        applied_for: "{{old('applied_for',\App\Models\Promotion::APPLICABLE_CATEGORIES) }}",
        is_load_data: false,
        target_type: '',
        categories: [],
        products: [],
        applied_for_ids: [],
        applicableFor(event) {
            let self = this
            let value = event.target.value;
            this.select2Initial()
            // console.log('value',value)
        },
        select2Initial() {
            this.$nextTick(() => {
                if ($(".select2").length > 0) {
                    $(".select2").select2();
                }
            });
        },
        async loadData() {
            let self = this;
            if (self.target_type == '') {
                self.end_date = ''
                alert('Please select target type')
                return
            }
            if (self.end_date == '') {
                alert('Please select end date')
                return
            }
            self.is_load_data = false
            self.categories = []
            self.products = []
            await $.ajax({
                url: `/promotion-valid-product-category`,
                method: 'get',
                dataType: 'json',
                data: {
                    target_type: self.target_type,
                    start_date: self.start_date,
                    end_date: self.end_date,
                },
                success: async (response) => {
                    self.is_load_data = true
                    self.categories = response.categories
                    self.products = response.products
                    // window.location.href = notify.data.visit_url
                    console.log('response', response)
                    self.select2Initial()

                },
                error: (error) => {
                    console.error('Error deleting location:', error);
                }
            });
        },
        init() {
            // this.loadMore();
            this.select2Initial()


        }
    }));
});
</script>
@endpush
