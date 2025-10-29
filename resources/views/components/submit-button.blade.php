@props(['value' => 'Submit'])
<x-button>
    <i class="fa fa-save"></i> {{ $value ?? $slot }}
</x-button>
