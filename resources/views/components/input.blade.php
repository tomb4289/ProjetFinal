@props([
    'label' => null,
    'name' => '',
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
])

{{-- Champ de formulaire réutilisable --}}
<div class="flex flex-col gap-1">
    @if($label)
        <label for="{{ $name }}" class="text-sm font-medium text-text-muted">
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
            rounded-lg
            px-3 py-2
            outline-none
            bg-card
            text-text-body
            border
            {{ $errors->has($name) ? 'border-red-500 bg-red-50' : 'border-muted' }}
            focus:ring-color-focus
            focus:ring-1
        "
    >

    @error($name)
        <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror

</div>
