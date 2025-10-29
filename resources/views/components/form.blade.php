@props([
    'method' => 'POST',
    'action' => '',
])
<form id="validate-form" method="{{ $method === 'GET' ? 'GET' : 'POST' }}" action="{{ $action }}"
    enctype="multipart/form-data" {{ $attributes }}>
    @csrf

    @if ($method == 'POST')
        @method('POST')
    @else
        @method($method)
    @endif

    {{ $slot }}
</form>
