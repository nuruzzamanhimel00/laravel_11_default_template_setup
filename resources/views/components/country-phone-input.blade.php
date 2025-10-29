@props([
    'disabled' => false,
    'value' => null,
    'name' => 'phone',
    'id' => 'phone',
    'placeholder' => 'Enter phone number',
    'initialCountry' => 'bd',
    'separateDialCode' => true,
    'preferredCountries' => [],
    'excludeCountries' => [],
    'onlyCountries' => [],
    'allowDropdown' => true,
    'hiddenInputName' => 'full_phone',
    'usesHiddenInput' => true
])

@php
    // Prioritize passed value, then fall back to old input if available
    $inputValue = $value ?? old($name) ?? '';
    // Generate a unique ID if multiple instances are used on the same page
    $uniqueId = $id . '_' . uniqid();
@endphp

<div class="intl-tel-input-container">
    <input
        id="{{ $uniqueId }}"
        {!! $attributes->merge([
            'class' => 'form-control',
            'placeholder' => $placeholder,
            'type' => 'tel',
            'name' => $name,
            'value' => $inputValue
        ]) !!}
        {{ $disabled ? 'disabled' : '' }}
    >

    @if($usesHiddenInput)
        <input type="hidden" name="{{ $hiddenInputName }}" id="{{ $uniqueId }}_full">
    @endif
</div>

@once
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    <style>
        .intl-tel-input-container .iti {
            width: 100%;
        }
    </style>
@endonce

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const input = document.querySelector("#{{ $uniqueId }}");
        const fullPhoneInput = document.querySelector("#{{ $uniqueId }}_full");

        const iti = window.intlTelInput(input, {
            initialCountry: "{{ $initialCountry }}",
            separateDialCode: {{ $separateDialCode ? 'true' : 'false' }},
            preferredCountries: @json($preferredCountries),
            excludeCountries: @json($excludeCountries),
            onlyCountries: @json($onlyCountries),
            allowDropdown: {{ $allowDropdown ? 'true' : 'false' }},
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
        });

        // Initialize with the correct format if we have a value
        if (input.value) {
            // Try to set the number programmatically
            try {
                const phoneNumber = input.value.trim();

                // Check if the number already includes a country code with +
                if (phoneNumber.startsWith('+')) {
                    iti.setNumber(phoneNumber);
                } else {
                    // If it doesn't have a country code, format it with the default country
                    const countryData = iti.getSelectedCountryData();
                    if (countryData && countryData.dialCode) {
                        // Set the formatted number
                        iti.setNumber("+" + countryData.dialCode + phoneNumber);
                    }
                }
            } catch (error) {
                console.warn('Error setting phone number:', error);
            }
        }

        // Update hidden field with the full international number when input changes
        if (fullPhoneInput) {
            const updateFullNumber = function() {
                fullPhoneInput.value = iti.getNumber();
            };

            input.addEventListener('blur', updateFullNumber);
            input.addEventListener('change', updateFullNumber);
            input.addEventListener('keyup', updateFullNumber);

            // Initialize with current value
            updateFullNumber();
        }

        // Add validation method
        input.iti = iti;
    });
</script>

