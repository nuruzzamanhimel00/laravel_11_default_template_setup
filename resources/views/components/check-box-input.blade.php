@props([
    'name',
    'label' => null,
    'type' => 'checkbox',
    'placeholder' => '',
    'required' => false,
    'value' => null,
    'checked' => false,
    'errorClass' => 'error',
    'class' => 'custom-control custom-radio custom-control-inline col-md-4',
    'onClick' => null // Added onClick prop"
])
<div class="d-flex align-items-center {{ $class }}">
    <input type="{{ $type }}" id="{{ $name }}_checkbox" name="{{ $name }}"
        value="{{ old($name, $value) }}" class="custom-control-input" {{ $checked ? 'checked' : '' }}
        {{ $required ? 'required' : '' }} {{ $onClick ? "onclick={$onClick}(event)" : '' }}  />
    <x-label :value="$label ?? __($name)" :is_required="$required" :required_indicator_class="$errorClass" for="{{ $name }}_checkbox"
        class="mb-0 mx-2 custom-control-label" />
</div>
