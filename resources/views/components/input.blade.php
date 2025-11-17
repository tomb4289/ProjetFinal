@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
])

<div class="flex flex-col gap-1">
    @if($label)
        <label for="{{ $name }}" class="text-sm font-medium text-text-heading">
            {{ $label }}
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}

        class="
            w-full
            rounded-md
            px-3 py-2
            outline-none
            bg-card
            text-text-body
            border
            {{ $errors->has($name) ? 'border-red-500 bg-red-50' : 'border-base' }}
            focus:border-primary
        "
    >

    @error($name)
        <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror

</div>
