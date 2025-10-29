@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-control', 'value' => old($attributes->get('name')), 'placeholder' => '', 'type' => 'text']) !!}>
