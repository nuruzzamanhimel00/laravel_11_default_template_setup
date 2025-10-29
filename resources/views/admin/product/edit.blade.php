@extends('admin.layouts.app')

@section('content')
<div class="col-lg-12 p-0">
    <div class="card">
        <div class="card-body">

            <x-form :action="route('products.update', $product->id)" method="PUT" data-parsley-validate>
                    <input type="hidden" name="low_stock_alert" value="{{LOW_STOCK_ALERT}}" />
                <div>
                    <div class="row">
                        <x-form-group name="name" value="{{ old('name', $product?->name) }}"
                            label="{{ __('Product name') }}" placeholder="Product Name"
                            groupClass="form-group col-xl-12 col-lg-12 p-2" required />
                        <x-form-group name="sku" value="{{ old('sku', $product?->sku) }}" label="{{ __('SKU') }}"
                            placeholder="Sku" groupClass="form-group col-xl-6 col-lg-6 p-2" />
                        <div class="col-xl-6 col-lg-6 p-2">
                            <div class="row">
                                <div class="form-group col-xl-8 col-lg-8">
                                    <x-label>{{ __('Barcode') }}</x-label>
                                    <div class="input-group">
                                        <input type="text" name="barcode" id="barcode" class="form-control"
                                            placeholder="Enter barcode" required
                                            value="{{ old('barcode', $product?->barcode ?? Carbon\Carbon::now()->timestamp) }}">
                                        <button type="button" class="btn btn-secondary p-2 generate-barcode"
                                            data-input="barcode" data-img-preview="b-image-show"
                                            data-img-value="barcode-value">Generate</button>
                                    </div>
                                    @error('barcode')
                                    <p class="error">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-xl-4 col-lg-4 d-flex align-items-center">
                                    <div class="pt-4">
                                        <img class="img-fluid max-width-50p barcode-image barcode-max-height"
                                            id="b-image-show" alt="barcode"
                                            style="width: 300px; height: 70px; object-fit:contain">
                                        <input id="barcode-value" type="hidden" name="barcode_image">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="form-group col-xl-6 col-lg-6 p-2">
                            <x-label :is_required="true">{{ __('Category') }}</x-label>
                            <select name="category_id" class="form-control">
                                <option>Select Category</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $product?->category_id) == $category->id ? 'selected' : ''}}>
                                    {{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div> --}}
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <label for="category_label">{{ __('Category') }} <span class="error">*</span></label>
                            <select name="category_id" class="form-select elect2_category_group" id="category_label">
                                <option>Select Category</option>
                                @foreach ($categories as $category)

                                <optgroup label="{{ $category->name }}">
                                    @foreach ($category->childs as $child)
                                    <option value="{{ $child->id }}" {{ old('category_id', $product?->category_id) == $child->id ? 'selected' : ''}}>{{ $child->name }}</option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>
                            @error('category_id')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <x-label>{{ __('Brands') }}</x-label>
                            <select name="brand_id" class="form-control">
                                <option>Select Brand</option>
                                @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ old('brand_id', $product?->brand_id) == $brand->id ? 'selected' : ''}}>
                                    {{ $brand->name }}</option>
                                @endforeach
                            </select>
                            @error('brand_id')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <x-label>{{ __('Units') }}</x-label>
                            <select name="product_unit_id" class="form-control">
                                <option>Select Unit</option>
                                @foreach ($units as $unit)
                                <option value="{{ $unit->id }}"
                                    {{ old('unit_id', $product?->product_unit_id) == $unit->id ? 'selected' : ''}}>
                                    {{ $unit->name }}</option>
                                @endforeach
                            </select>
                            @error('product_unit_id')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <x-form-group name="unit_value"
                            value="{{ old('unit_value', $product?->productMeta?->unit_value) }}"
                            label="{{ __('Unit Value') }}" placeholder="Unit Value"
                            groupClass="form-group col-xl-6 col-lg-6 p-2" />

                        <x-status-group name="taxes[has_tax]" group_label="Tax/Vat"
                            value="{{ old('taxes.has_tax', $product?->taxes?->has_tax) }}" active_label="Include"
                            inactive_label="Exclude" active_value="1" inactive_value="0"
                            main_div_class="col-xl-6 col-lg-6 p-4" onClick="handleTaxesChange" />

                        <div id="unit_value_section" class="form-group col-xl-6 col-lg-6 p-2">
                            <x-label>{{ __('Custom tax amount') }}(%)</x-label>
                            <x-input type="number" name="taxes[tax_amount]" class="form-control"
                                value="{{ old('taxes.tax_amount', $product?->taxes?->tax_amount) }}" />
                            @error('taxes.tax_amount')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-xl-12 col-lg-12 p-2">
                            <x-label>{{ __('Notes') }}</x-label>
                            <x-text-input name="notes" value="{{ old('notes', $product?->productMeta?->notes) }}" />
                            @error('notes')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- <x-image-upload name="image" label="Thumbnail (140 * 140 px)"
                            oldImage="{{ old('image', getStorageImage($product?->image)) }}" /> --}}

                        <div class="form-group col-xl-6 col-lg-6 p-2">

                                <x-image-upload name="image" label="Thumbnail (140 * 140 px)" oldImage="{{ old('image', getStorageImage($product?->image)) }}" />
                            </div>
                            <div class="form-group col-xl-6 col-lg-6 p-2">
                                <x-image-upload name="details_image" label="Details Image (300 * 220 px)" oldImage="{{ old('details_image', getStorageImage($product?->details_image)) }}" />
                            </div>

                        <div class="form-group col-xl-12 col-lg-12 p-2">
                            <x-label>{{ __('Description') }}</x-label>
                            <textarea name="description" class="form-control summernote"
                                placeholder="description">{{old('description', $product?->productMeta?->description)}}</textarea>
                            @error('description')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- <div class="form-group col-xl-6 col-lg-6 p-2">
                            <x-label>{{ __('Tags') }}</x-label>

                            <div class="ic-select-custom">
                                <select class="form-control select2_tags" multiple="multiple" name="product_tags[]">
                                @if($product->product_tags)
                                @foreach($product->product_tags as $tag)
                                <option  selected>{{ $tag->name }}</option>
                                @endforeach
                                @endif
                            </select>
                            </div>
                            @error('product_tags.*')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div> --}}
                        <div class="form-group col-xl-6 col-lg-6 p-2">
                            <x-label>{{ __('Tags') }}</x-label>

                            <div class="ic-select-custom">

                                <select class="form-control select2_tags" multiple="multiple" name="product_tags[]">
                                    @if($product->product_tags)
                                    @foreach($product->product_tags as $tag)
                                    <option  selected>{{ $tag->name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>

                            @error('product_tags.*')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <x-status-group value="{{ old('status', $product?->status) }}"
                        main_div_class="col-xl-6 col-lg-6 p-2" is_required="true" />
                    <div class="form-group col-xl-6 col-lg-6 p-2">
                        <x-label :is_required="true">{{ __('Available For') }}</x-label>
                        <div class="row">
                            <x-check-box-input name="available_for" type="radio" label="Customer"
                                :value="old('available_for', 'Customer')"
                                checked="{{ old('available_for', $product?->available_for) == 'Customer' ? true : false }}" />

                            <x-check-box-input name="available_for" type="radio" label="Restaurant"
                                :value="old('available_for','Restaurant')"
                                checked="{{ old('available_for', $product?->available_for) == 'Restaurant' ? true : false }}" />

                            <x-check-box-input name="available_for" type="radio" label="Both"
                                :value="old('available_for','Both')"
                                checked="{{ old('available_for', $product?->available_for) == 'Both' ? true : false }}" />
                        </div>
                        @error('available_for')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group col-xl-6 col-lg-6 p-2">
                        <div class="row">
                            <x-check-box-input name="is_split_sale" type="checkbox" label="Is Split sale"
                                :value="old('is_split_sale', 1)"
                                checked="{{ old('is_split_sale', $product?->is_split_sale) == 1 ? true : false }}" />

                        </div>
                        @error('is_split_sale')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <x-form-submit-buttons cancel_url="{{ route('products.index') }}" />
            </x-form>
        </div>
    </div>
</div> <!-- end col -->
@endsection

@push('style')
@endpush
@push('script')
<script src="{{ asset('libs/JsBarcode/JsBarcode.all.min.js') }}"></script>
{{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function handleTaxesChange(e) {
    const {
        value,
        name
    } = event.target;
    // toggle class display none
    const section = document.getElementById('unit_value_section');
    if (value == 1) {
        section.classList.remove('d-block');
        section.classList.remove('d-none');
        section.classList.add('d-block')
    } else {
        section.classList.remove('d-block');
        section.classList.remove('d-none');
        section.classList.add('d-none')
    }
}
$(document).ready(function() {
    if ($(".select2_category_group").length > 0) {
                $('.select2_category_group').select2({

                    placeholder: 'Select a category',
                    allowClear: true
                });
            }
        if ($(".select2_tags").length > 0) {

            $(".select2_tags").select2({
                tags: true,
                    tokenSeparators: [',', ' ']
            });
        }
})
</script>
@endpush
