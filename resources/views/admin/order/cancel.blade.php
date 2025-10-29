@extends('admin.layouts.app')

@section('content')
    <div class="col-lg-12 p-0">
        <div class="card">
            <div class="card-body">

                <form action="{{ route('order.cancel.update',$id) }}" method="post" enctype="multipart/form-data"
                    data-parsley-validate>
                    @csrf

                    <div class="row">

                        <div class="form-group col-md-6 p-2">
                            <label>{{ __('Date') }} <span class="error">*</span></label>
                            <input type="date" name="cancel_date" class="form-control" required
                                value="{{date('Y-m-d')  }}" id="cancel_date" min="{{date('Y-m-d') }}">

                            @error('cancel_date')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-md-6 p-2">
                            <label for="notes">{{ __('Notes') }} </label>
                            <div>
                                <textarea class="form-control" id="cancel_note" rows="3" name="cancel_note" required></textarea>
                            </div>
                            @error('cancel_note')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group mt-4">
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-primary waves-effect waves-lightml-2" type="submit">
                                <i class="fa fa-save"></i> {{ __('Submit') }}
                            </button>
                            <a class="btn btn-danger waves-effect" href="{{ route('orders.index') }}">
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
<script>

    $(document).ready(function () {

    });
</script>
@endpush
