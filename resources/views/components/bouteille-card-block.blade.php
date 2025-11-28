@props([
    'id' => '', 
    'nom' => '', 
    'image' => null, 
    'prix' => '', 
    'mode' => 'catalogue',
    'cellierId' => null,
    'bouteilleId' => null,
    'quantite' => 1,
    'pays' => null,
    'format' => null,
    'codeSaq' => null
])

@php
    $isCellierMode = $mode === 'cellier';
    $isCatalogueMode = $mode === 'catalogue';
@endphp

{{-- Carte de bouteille --}}
<div class='flex flex-col justify-between bg-card rounded-lg shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden'>
    {{-- Image cliquable --}}
    @if($isCatalogueMode)
        <a href="{{ route('catalogue.show', $id) }}" class="flex flex-col">
    @elseif($isCellierMode)
        <a href="{{ route('bouteilles.show', [$cellierId, $bouteilleId]) }}" class="flex flex-col">
    @endif
    
    <picture class='w-full h-32 overflow-hidden bg-neutral-400 flex items-center justify-center cursor-pointer'>
        @if ($image === null)
            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        @else
            <img src='{{ $image }}' alt='Image de la bouteille {{ $nom }}' class='w-full h-full object-contain object-center hover:scale-105 transition-transform duration-300'/>
        @endif
    </picture>
    
    @if($isCatalogueMode || $isCellierMode)
        </a>
    @endif
    
    <div class='p-4 flex flex-col gap-2'>
        <div class="flex flex-col items-start  gap-2 ">
            <span class="font-semibold text-text-title text-lg truncate overflow-hidden text-ellipsis whitespace-nowrap w-full block">
            {{ $nom }}
            </span>
            
            @if($isCellierMode)
                {{-- Contrôles quantité + badge --}}
                <div class="flex  items-center gap-2 stop-link-propagation">
                    {{-- Bouton - --}}
                    <button
                        type="button"
                        class="qty-btn bottle-qty-minus inline-flex items-center justify-center w-7 h-7 rounded-full border border-border-base text-primary hover:bg-primary/10"
                        data-url="{{ route('bouteilles.quantite.update', [$cellierId, $bouteilleId]) }}"
                        data-direction="down"
                        data-bouteille="{{ $bouteilleId }}"
                        data-qty-btn
                        data-cellier-id="{{ $cellierId }}"
                        data-bottle-id="{{ $bouteilleId }}"
                    >
                        –
                    </button>

                    {{-- Badge quantité --}}
                    <div
                        class="qty-display bottle-qty-value inline-flex items-center justify-center rounded-full bg-primary text-white text-xs px-2 py-0.5 min-w-16 text-center"
                        data-bouteille="{{ $bouteilleId }}"
                        data-qty-value="{{ $bouteilleId }}"
                    >
                        x {{ $quantite ?? 1 }}
                    </div>

                    {{-- Bouton + --}}
                    <button
                        type="button"
                        class="qty-btn bottle-qty-plus inline-flex items-center justify-center w-7 h-7 rounded-full border border-border-base text-primary hover:bg-primary/10"
                        data-url="{{ route('bouteilles.quantite.update', [$cellierId, $bouteilleId]) }}"
                        data-direction="up"
                        data-bouteille="{{ $bouteilleId }}"
                        data-qty-btn
                        data-cellier-id="{{ $cellierId }}"
                        data-bottle-id="{{ $bouteilleId }}"
                    >
                        +
                    </button>
                </div>
            @endif
        </div>

        @if($isCellierMode)
            {{-- Informations supplémentaires pour le mode cellier --}}
            <div class="text-sm text-text-muted space-y-1">
                @if ($pays)
                    <p>
                        <span class="font-medium text-text-body">Pays :</span>
                        {{ $pays }}
                    </p>
                @endif

                @if (!is_null($prix))
                    <p>
                        <span class="font-medium text-text-body">Prix :</span>
                        {{ number_format($prix, 2, ',', ' ') }} $
                    </p>
                @endif
            </div>
        @else
            {{-- Prix simple pour le mode catalogue --}}
            <span class='text-text-muted'>{{ $prix }} $</span>
        @endif

        {{-- Actions --}}
        <div class="flex gap-2 {{ $isCellierMode ? 'mt-auto' : 'flex-row-reverse flex-wrap justify-end' }}">
            @if($isCatalogueMode)
                {{-- Formulaire d'ajout au cellier --}}
                <form class="flex gap-3 flex-row-reverse flex-wrap justify-end add-to-cellar-form w-full">
                    <input 
                        type="hidden" 
                        name="bottle_id" 
                        value="{{ $id }}"
                    >

                    <input 
                        type="number"
                        name="quantity"
                        min="1"
                        max="10"
                        value="1"
                        class="flex-1 text-center border border-gray-300 rounded-lg px-2 py-1"
                    />

                    <button 
                        type="button"
                        class="flex-3 add-to-cellar-btn bg-primary active:bg-primary-active hover:bg-primary-hover animation duration-200 text-white rounded-lg px-4 py-2"
                        data-bottle-id="{{ $id }}"
                    >
                        Ajouter
                    </button>
                </form>
            @else
                {{-- Boutons edit/delete pour le mode cellier --}}
                <x-delete-btn 
                    :route="route('bouteilles.delete', [
                        'cellier'   => $cellierId,
                        'bouteille' => $bouteilleId,
                    ])"
                />

                {{-- Afficher le bouton Modifier uniquement pour les bouteilles ajoutées manuellement (sans code SAQ) --}}
                @if (empty($codeSaq))
                    <x-edit-btn
                        :route="route('bouteilles.edit', [$cellierId, $bouteilleId])"
                        label="Modifier"
                    />
                @endif
            @endif
        </div>
    </div>
</div>