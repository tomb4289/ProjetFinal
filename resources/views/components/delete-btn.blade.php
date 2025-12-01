@props(['route' => null, 'id' => null])
{{-- Bouton de suppression avec confirmation --}}
<form 
    action="{{ $route }}" 
    method="POST"
    @if($id) id="{{ $id }}" @endif
    class="inline"
>
    @csrf
    @method('DELETE')

    <button 
        type="button"
        class="use-confirm p-2 bg-card hover:bg-card-hover rounded-lg border-border-base border shadow-md hover:shadow-sm transition-all duration-300"
        data-action="{{ $route }}"
        aria-label="Supprimer"
    >
        <x-dynamic-component :component="'lucide-trash-2'" class="w-6 stroke-text-heading"/>
    </button>
</form>