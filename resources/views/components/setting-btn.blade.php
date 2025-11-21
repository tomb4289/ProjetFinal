@props(["id" => "id"])
{{-- Bouton de paramètres --}}
<button id="{{ $id }}"  class="p-2 bg-card hover:bg-card-hover rounded-lg border-border-base border shadow-md hover:shadow-sm transition-all duration-300">
    <x-dynamic-component :component="'lucide-settings'" class="w-6 stroke-text-heading "/>
</button>