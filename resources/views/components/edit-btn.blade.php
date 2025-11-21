{{-- Bouton d'édition --}}
@props(["id" => null, "route" => null])
<a href="{{ $route }}" class="p-2 bg-card hover:bg-card-hover rounded-lg border-border-base border shadow-md hover:shadow-sm transition-all duration-300">
    <x-dynamic-component :component="'lucide-pen'" class="w-6 stroke-text-heading "/>
</a>