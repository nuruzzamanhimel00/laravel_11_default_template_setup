@props(['url' => '#', 'value' => __('Cancel')])

<a  {{ $attributes->merge(['class' => 'btn btn-danger waves-effect']) }} href="{{ $url }}">
    <i class="fa fa-times"></i> {{ $value ?? $slot }}
</a>
