@props([
    'label' => 'Click Me',
    'type' => 'button',     // button | submit | href
    'route' => null,
    'rounded' => 'lg',
    'id' => null,
    'data' => null,
])
{{-- Bouton principal --}}
@php
    $classes = "bg-button-default text-white font-bold py-2 px-4 rounded-{$rounded} 
                hover:bg-button-hover transition-colors duration-300 block text-center";

    // Détection automatique :
    if ($route) {
    $href = route($route);
    } else {
        $href = '#';
    }
@endphp


@if ($type === 'href')
    {{-- Génère un <a> --}}
    <a href="{{ $href }}"
       {{ $attributes->merge(['id' => $id, 'class' => $classes]) }}>
        {{ $label }}
    </a>

@else
    {{-- Génère un <button> --}}
    <button type="{{ $type }}"
        {{ $attributes->merge(['id' => $id,'class' => $classes]) }}>
        {{ $label }}
    </button>
@endif
