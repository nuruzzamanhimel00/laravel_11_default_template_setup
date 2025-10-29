@props([
    'value' => null,
    'is_required' => false,
    'required_indicator_class' => 'error',
])

<label {!! $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) !!}>
    @if($value)
        {{ $value }}
    @else
        {{ $slot }}
    @endif

    @if($is_required)
        <span class="{{ $required_indicator_class }} ml-1">*</span>
    @endif
</label>
