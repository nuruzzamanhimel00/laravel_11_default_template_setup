@props(['cancel_url' => '#', 'submit_btn' => __('Submit')])
<div class="form-group">
    <div>
        <x-button>
            <i class="fa fa-save"></i> {{ $submit_btn }}
        </x-button>
        <x-cancel-button url="{{ $cancel_url }}" />
    </div>
</div>
