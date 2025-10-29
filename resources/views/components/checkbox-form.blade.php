@props([
    'name',
    'label' => null,
    'type' => 'checkbox',
    'placeholder' => '',
    'required' => false,
    'value' => null,
    'checked' => false,
    'errorClass' => 'error',
    'groupClass' => 'col-xl-12 col-lg-12 p-2',
    'checkboxWrapperClass' => 'custom-control custom-radio custom-control-inline',
    'onClick' => null // Added onClick prop"
])
<div class="{{ $groupClass }}">
    <div class="{{ $checkboxWrapperClass }}">
            <input type="{{ $type }}"
                   id="{{ $name }}_checkbox"
                   name="{{ $name }}"
                   value="{{ old($name, $value) }}"
                   class="custom-control-input"
                   {{ $checked ? 'checked' : ''}}
                   {{ $required ? 'required' : '' }}
                   {{ $onClick ? "onclick={$onClick}(event)" : '' }}
                />

            {{-- <label class="custom-control-label" for="{{ $option['id'] }}">
                {{ $option['label'] }}
            </label>

        <x-input value="{{ old($name, $value) }}" :type="$type" :name="$name" id="{{ $name }}_checkbox" :required="$required"
            :checked="$checked || old($name)" class="form-check-input mr-2" {{ $attributes }} /> --}}

        <x-label :value="$label ?? __($name)" :is_required="$required" :required_indicator_class="$errorClass" for="{{ $name }}_checkbox"
            class="custom-control-label" />
    </div>

    @error($name)
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
</div>
