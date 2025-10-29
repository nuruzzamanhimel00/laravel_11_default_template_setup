@extends('admin.layouts.app')

@section('content')
<div class="col-lg-6 p-0">
    <div class="card">
        <div class="card-body" id="vueApp" >
            {{-- <h4 class="header-title">{{ __('Add Brand') }}</h4> --}}
            <form action="{{ route('product.reviews.update',$review->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <span>
                    <edit-review :review="{{ json_encode($review) }}" />

                </span>
                <div class="form-group col-xl-12 col-lg-12 p-2">
                    <div class="row">
                        <label class="d-block mb-3 col-md-12">{{ __('Status') }} <span class="error">*</span></label>
                        <div class="custom-control custom-radio custom-control-inline col-md-3">
                            <input type="radio" id="status_yes" value="{{ \App\Models\User::STATUS_ACTIVE }}"
                                name="status" class="custom-control-input" {{ old('status', $review->status) == \App\Models\User::STATUS_ACTIVE ? 'checked' : ''}}>
                            <label class="custom-control-label" for="status_yes">{{ __('Active') }}</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline col-md-4">
                            <input type="radio" id="status_no" value="{{ \App\Models\User::STATUS_INACTIVE }}"
                                name="status" class="custom-control-input" {{ old('status', $review->status) == \App\Models\User::STATUS_INACTIVE ? 'checked' : ''}}>
                            <label class="custom-control-label" for="status_no">{{ __('Inactive') }}</label>
                        </div>
                    </div>


                    @error('status')
                    <p class="error">{{ $message }}</p>
                    @enderror
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


    {{-- <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reviewData', (review) => ({
                images:[],
                review,
                initialize() {
                    let self = this
                    if(self.review.images && self.review.images.length > 0){
                        self.images = self.review.images
                    }
                },
                init() {
                    this.initialize();
                    this.$nextTick(() => {
                        // $(".my-rating").starRating({
                        // initialRating: 4,
                        // strokeColor: '#894A00',
                        // strokeWidth: 10,
                        // starSize: 25
                        // });
                    });


                }
            }));
        });
    </script> --}}
@endpush
