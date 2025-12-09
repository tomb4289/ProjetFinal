@props([
    'label' => null,
    'hiddenLabel' => false,
    'name' => '',
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'min' => null,
    'max' => null,
    "size" => null,
])

{{-- Champ de formulaire réutilisable --}}
<div class="flex flex-col gap-1 flex-1 {{ $size === 'full' ? 'w-full' : '' }}">
    @if($label)
        <label for="{{ $name }}" class="{{ $hiddenLabel ? 'sr-only' : 'text-sm font-medium text-text-muted' }}">
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
        {{ $min !== null ? 'min=' . $min : '' }}
        {{ $max !== null ? 'max=' . $max : '' }}
        
        {{-- Accessibilité --}}
        aria-required="{{ $required ? 'true' : 'false' }}"
        aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}"
        @if($errors->has($name)) aria-describedby="error-{{ $name }}" @endif

        class="
            {{ $attributes -> get('class') }}
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
    />        

    @error($name)
        {{-- role="alert" assure que l'erreur est lue immédiatement par le lecteur d'écran --}}
        <p id="error-{{ $name }}" role="alert" class="text-red-600 text-sm">{{ $message }}</p>
    @enderror

</div>