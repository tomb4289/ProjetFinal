@props([
    'route' => null,     // Peut être un name de route ou un href complet
    'label' => 'Retour',
    'icon'  => true,     // Affiche une icône si tu veux
])

@php
    // Si tu fournis un name de route Laravel (ex: "admin.users.index")
    if ($route && !str_contains($route, 'http') && !str_contains($route, '/')) {
        $href = route($route);
    }
    // Si tu fournis un href complet ("/admin/users" ou "https://...")
    elseif ($route) {
        $href = $route;
    }
    // Sinon → on revient à la page précédente
    else {
        $href = url()->previous();
    }

    $classes = "inline-flex items-center gap-2 
                text-button-default font-medium 
                hover:text-button-hover 
                transition-all duration-200";
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        {{-- Icône décorative ignorée par les lecteurs d'écran --}}
        <x-dynamic-component
            :component="'lucide-arrow-left'"
            class="w-4 h-4"
            aria-hidden="true"
        />
    @endif

    {{ $label }}
</a>
