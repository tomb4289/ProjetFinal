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
    $classes = "bg-button-default border-2 border-primary text-primary font-bold py-2 px-4 rounded-{$rounded} 
                hover:bg-button-hover hover:text-white active:bg-primary-active transition-colors duration-300 block text-center";

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
       {{ $attributes->merge(['id' => $id, 'class' => $classes]) }}
       aria-label="{{ $label }}">
        {{ $label }}
    </a>

@else
    {{-- Génère un <button> --}}
    <button type="{{ $type }}"
        {{ $attributes->merge(['id' => $id,'class' => $classes]) }}
        aria-label="{{ $label }}">
        {{ $label }}
    </button>
@endif