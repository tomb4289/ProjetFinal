@props([
    'id' => null,
    'isModifiable' => null,
    'deleteUrl' => null,
    'editUrl' => null,
    {{-- AJOUT TRANSFERT --}}
    'transferUrl' => null,
    'item' => null,
    'celliersCount' => 0,
    "absolute" => true,
])

<div class="group {{ $absolute ? 'absolute top-0 right-0' : 'relative' }} p-2 cursor-pointer z-20 ">

    {{-- Bouton --}}
    <button type="button" class="cursor-pointer" aria-haspopup="true" aria-label="Options" onclick="event.stopPropagation(); toggleDropdown('dropdownActionBtn-{{ $id }}')">
        <x-dynamic-component :component="'lucide-more-vertical'" class="w-6 stroke-text-heading"/>
    </button>

    {{-- Menu Déroulant --}}
    <div id="dropdownActionBtn-{{ $id }}" 
        class="hidden absolute right-0 top-6 z-40 p-2 rounded-lg border border-border-base bg-card min-w-[120px] shadow-lg"
        role="menu"
    >
        {{-- Option Modifier --}}
        @if($editUrl)
            <a href="{{ $editUrl }}" class="block px-2 py-1 text-sm hover:bg-gray-100 rounded transition-colors mb-1" role="menuitem">
                Modifier
            </a>
        @endif

        {{-- AJOUT TRANSFERT – Option TRANSFÉRER --}}
        @if($transferUrl)
            <button
                type="button"
                class="wishlist-transfer-btn block w-full text-left px-2 py-1 text-sm hover:bg-gray-100 rounded transition-colors mb-1"
                data-item-id="{{ $item->id }}"
                data-url="{{ $transferUrl }}"
                role="menuitem"
            >
                {{ $celliersCount === 1 ? 'transférer au cellier' : 'transférer à un cellier' }}
            </button>
        @endif

        {{-- Option Supprimer --}}
        @if($deleteUrl)
            <button 
                type="button" 
                class="use-confirm block w-full text-left px-2 py-1 text-sm text-red-600 hover:bg-red-50 rounded transition-colors"
                data-action="{{ $deleteUrl }}"
                data-ajax="true"
                role="menuitem"
            >
                Supprimer
            </button>
        @endif
    </div>
    
</div>