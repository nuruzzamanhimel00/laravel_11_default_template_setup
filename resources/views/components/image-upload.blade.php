@props([
    'name',
    'label' => null,
    'required' => false,
    'errorClass' => 'error',
    'groupClass' => 'form-group col-xl-12 col-lg-12 p-2',
    'previewClass' => 'img-preview mt-2 rounded border',
    'oldImage' => null,
    'acceptTypes' => 'image/*',
    'previewWidth' => '150px',
    'previewHeight' => 'auto',
    'placeholderImage' => '/images/default/noimage.png',
    "is_show_preview" => false
])

@php
    $uniqueId = $name . '_' . uniqid();
@endphp

<div class="{{ $groupClass }}">
    <x-label
        :value="$label ?? __($name)"
        :is_required="$required"
        :required_indicator_class="$errorClass"
    />

    <div class="image-upload-container">
        <div class="image-upload-wrapper">
            <!-- File input -->
            <input
                type="file"
                id="{{ $uniqueId }}_upload"
                name="{{ $name }}"
                accept="{{ $acceptTypes }}"
                class="image-upload-input"
                style="display: none;"
                {{ $required ? 'required' : '' }}
                {{ $attributes }}
            >

            <!-- Upload button -->
            <div class="d-flex align-items-center mb-3">
                <button
                    type="button"
                    class="btn btn-success me-2"
                    onclick="document.getElementById('{{ $uniqueId }}_upload').click();"
                >
                    <i class="fas fa-upload mr-1"></i> {{ __('Choose Image') }}
                </button>
                <span class="selected-file-name text-muted" id="{{ $uniqueId }}_filename"></span>
            </div>

            <!-- Preview -->
            <div class="image-preview-container">
                <div class="new-image mb-2" id="{{ $uniqueId }}_container" style="{{ $oldImage ? '' : 'display: none;' }}">
                    <p class="text-sm text-muted mb-1">{{ $oldImage ? __('Image preview:') : __('New image:') }}</p>
                    <img
                        src="{{ $oldImage ? $oldImage : '' }}"
                        alt="{{ __('Image preview') }}"
                        class="{{ $previewClass }}"
                        id="{{ $uniqueId }}_preview"
                        style="max-width: {{ $previewWidth }}; max-height: {{ $previewHeight }}; {{ !$oldImage ? 'display: none;' : '' }}"
                    >
                </div>
            </div>
        </div>
    </div>

    @error($name)
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
</div>

<!-- Scoped Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('{{ $uniqueId }}_upload');
        const newPreview = document.getElementById('{{ $uniqueId }}_preview');
        const newImageContainer = document.getElementById('{{ $uniqueId }}_container');
        const fileNameDisplay = document.getElementById('{{ $uniqueId }}_filename');

        fileInput.addEventListener('change', function() {
            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    newPreview.src = e.target.result;
                    newPreview.style.display = 'block';
                    newImageContainer.style.display = 'block';
                    fileNameDisplay.textContent = fileInput.files[0].name;
                }

                reader.readAsDataURL(fileInput.files[0]);
            } else {
                newPreview.src = '';
                newPreview.style.display = 'none';
                newImageContainer.style.display = '{{ $oldImage ? "block" : "none" }}';
                fileNameDisplay.textContent = '';
            }
        });
    });
</script>
