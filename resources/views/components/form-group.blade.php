@props([
    'name',
    'label' => null,
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'value' => null,
    'checked' => false,
    'errorClass' => 'error',
    'groupClass' => 'form-group col-xl-12 col-lg-12 p-2',
    'checkboxWrapperClass' => 'form-check d-flex align-items-center'
])

<div class="{{ $groupClass }}">
    @if($type === 'checkbox')
        <div class="{{ $checkboxWrapperClass }}">
            <x-input
                :type="$type"
                :name="$name"
                id="{{ $name }}_checkbox"
                :required="$required"
                :checked="$checked || old($name)"
                class="form-check-input mr-2"
                {{ $attributes }}
            />

            <x-label
                :value="$label ?? __($name)"
                :is_required="$required"
                :required_indicator_class="$errorClass"
                for="{{ $name }}_checkbox"
                class="form-check-label mb-0"
            />
        </div>
    @else
        <x-label
            :value="$label ?? __($name)"
            :is_required="$required"
            :required_indicator_class="$errorClass"
        />

        <x-input
            :type="$type"
            :name="$name"
            :placeholder="$placeholder ?: __($name)"
            :required="$required"
            :value="$value ?? old($name)"
            {{ $attributes }}
        />
    @endif

    @error($name)
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
</div>
