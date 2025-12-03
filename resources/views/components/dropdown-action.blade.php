@props([
    'id' => null,
    'isModifiable' => null,
    'deleteUrl' => null,
    'editUrl' => null,
])

<div class="group absolute top-0 right-0 p-2 cursor-pointer z-20 ">

    {{-- Bouton --}}
    <button class="cursor-pointer" aria-haspopup="true" aria-label="Options">
        <x-dynamic-component :component="'lucide-more-vertical'" class="w-6 stroke-text-heading"/>
    </button>

    {{-- Menu Déroulant --}}
    <div id="dropdownActionBtn-{{ $id }}" 
        class="hidden group-hover:block group-focus-within:block absolute right-0 top-6 z-40 p-2 rounded-lg border border-border-base bg-card min-w-[120px] shadow-lg"
        role="menu"
    >
        {{-- Option Modifier --}}
        @if($editUrl)
            <a href="{{ $editUrl }}" class="block px-2 py-1 text-sm hover:bg-gray-100 rounded transition-colors mb-1" role="menuitem">
                Modifier
            </a>
        @endif

        {{-- Option Supprimer --}}
        @if($deleteUrl)
            <button 
                type="button" 
                class="use-confirm block w-full text-left px-2 py-1 text-sm text-red-600 hover:bg-red-50 rounded transition-colors"
                data-action="{{ $deleteUrl }}"
                role="menuitem"
            >
                Supprimer
            </button>
        @endif
    </div>
    
</div>