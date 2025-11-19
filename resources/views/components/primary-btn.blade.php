@props([
    'label' => 'Click Me',
    'type' => 'button',     // button | submit | href
    'route' => null,
    'rounded' => 'lg'
])

@php
    $classes = "bg-primary text-white font-bold py-2 px-4 rounded-{$rounded} 
                hover:bg-primary-hover transition-colors duration-300 block text-center";

    // Détection automatique :
    // - si route() est un NAME → on génère un URL
    // - si c'est déjà une URL → on ne touche pas
    if ($route) {
        $href = str_starts_with($route, 'http')
            ? $route
            : route($route);
    } else {
        $href = '#';
    }
@endphp

@if ($type === 'href')
    {{-- Génère un <a> --}}
    <a href="{{ $href }}"
       {{ $attributes->merge(['class' => $classes]) }}>
        {{ $label }}
    </a>

@else
    {{-- Génère un <button> --}}
    <button type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes]) }}>
        {{ $label }}
    </button>
@endif
