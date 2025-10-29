@props([
    'group_label' => __('Status'),
    'is_required' => false,
    'active_value' => STATUS_ACTIVE,
    'inactive_value' => STATUS_INACTIVE, // Changed to explicit value
    'active_label' => __('Active'),
    'inactive_label' => __('Inactive'),
    'name' => 'status', // Corrected default name
    'main_div_class' => 'col-xl-12 col-lg-12',
    'value' => null, // Added current value prop,
    'onClick' => null // Added onClick prop"
])

@php
    $options = [
        [
            'id' => "{$name}_active",
            'value' => $active_value,
            'label' => $active_label,
        ],
        [
            'id' => "{$name}_inactive",
            'value' => $inactive_value,
            'label' => $inactive_label,
        ]
    ];
@endphp

<div class="form-group p-2 {{ $main_div_class }}">
    <label class="d-block mb-3">
        {{ $group_label }}
        @if($is_required)
            <span class="error">*</span>
        @endif
    </label>

    <div class="row">
        @foreach($options as $option)
            <div class="d-flex align-items-center custom-control custom-radio custom-control-inline col-md-4">
                <input type="radio"
                       id="{{ $option['id'] }}"
                       name="{{ $name }}"
                       value="{{ $option['value'] }}"
                       class="custom-control-input"
                       {{ $value == $option['value'] ? 'checked' : '' }}
                       {{ $is_required ? 'required' : '' }}
                       {{ $onClick ? "onclick={$onClick}(event)" : '' }}

                />

                <label class="custom-control-label mb-0 mx-2" for="{{ $option['id'] }}">
                    {{ $option['label'] }}
                </label>
            </div>
        @endforeach
    </div>

    @error($name)
        <p class="error">{{ $message }}</p>
    @enderror
</div>
