@props([
    'disabled' => false,
    'value' => ''
])
<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-control', 'placeholder' => '']) !!}>{{ $value }}</textarea>
