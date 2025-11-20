@props([
    'route' => null,     // Peut être un name de route ou un href complet
    'label' => 'Retour',
    'icon' => true,      // Affiche une icône si tu veux
])

@php
    // Si tu fournis une route Laravel, on génère le vrai lien
    if ($route && !str_contains($route, 'http') && !str_contains($route, '/')) {
        $href = route($route);
    }
    // Si tu fournis un href manuel
    elseif ($route) {
        $href = $route;
    }
    // Sinon → retour automatique
    else {
        $href = url()->previous();
    }

    $classes = "inline-flex items-center gap-2 text-primary font-medium 
                hover:text-primary-hover transition-all duration-200";
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <x-lucide-arrow-left class="w-4 h-4" />
    @endif

    {{ $label }}
</a>
