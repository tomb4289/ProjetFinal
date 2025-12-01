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
<div class='relative flex flex-col justify-between bg-card rounded-lg shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden' role="article" aria-label="Carte de la bouteille {{ $nom }}">
    
    @if($isCellierMode)
        <x-dropdown-action 
            :id="$bouteilleId" 
            :deleteUrl="route('bouteilles.delete', [$cellierId, $bouteilleId])" 
            :editUrl="empty($codeSaq) ? route('bouteilles.edit', [$cellierId, $bouteilleId]) : null" 
        />
    @endif

    {{-- Image cliquable --}}
    @if($isCatalogueMode)
        <a href="{{ route('catalogue.show', $id) }}" class="flex flex-col" aria-label="Voir les détails de {{ $nom }}">
    @elseif($isCellierMode)
        <a href="{{ route('bouteilles.show', [$cellierId, $bouteilleId]) }}" class="flex flex-col" aria-label="Voir les détails de {{ $nom }}">
    @endif
    
    <picture class='w-full h-32 overflow-hidden bg-neutral-400 flex items-center justify-center cursor-pointer'>
        @if ($image === null)
            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-label="Aucune image disponible">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        @else
            <img src='{{ $image }}' alt='Bouteille {{ $nom }}' class='w-full h-full object-contain object-center hover:scale-105 transition-transform duration-300'/>
        @endif
    </picture>
    
    @if($isCatalogueMode || $isCellierMode)
        </a>
    @endif
    
    <div class='p-4 flex flex-col gap-2'>
        <div class="flex flex-col items-start gap-2 ">
            <span class="font-semibold text-text-title text-lg truncate overflow-hidden text-ellipsis whitespace-nowrap w-full block" title="{{ $nom }}">
            {{ $nom }}
            </span>
            
            @if($isCellierMode)
                {{-- Contrôles quantité + badge --}}
                <div class="flex items-center gap-2 stop-link-propagation" role="group" aria-label="Contrôle de la quantité">
                    {{-- Bouton - --}}
                    <button
                        type="button"
                        class="qty-btn bottle-qty-minus inline-flex items-center justify-center w-7 h-7 rounded-full border border-border-base text-button-default hover:text-button-hover hover:bg-button-hover/10 transition"
                        data-url="{{ route('bouteilles.quantite.update', [$cellierId, $bouteilleId]) }}"
                        data-direction="down"
                        data-bouteille="{{ $bouteilleId }}"
                        data-qty-btn
                        data-cellier-id="{{ $cellierId }}"
                        data-bottle-id="{{ $bouteilleId }}"
                        aria-label="Diminuer la quantité"
                    >
                        –
                    </button>

                    {{-- Badge quantité --}}
                    <div
                        class="qty-display bottle-qty-value inline-flex items-center justify-center rounded-full bg-primary text-white text-xs px-2 py-0.5 min-w-16 text-center"
                        data-bouteille="{{ $bouteilleId }}"
                        data-qty-value="{{ $bouteilleId }}"
                        aria-label="Quantité actuelle : {{ $quantite ?? 1 }}"
                        role="status"
                    >
                        x {{ $quantite ?? 1 }}
                    </div>

                    {{-- Bouton + --}}
                    <button
                        type="button"
                        class="qty-btn bottle-qty-plus inline-flex items-center justify-center w-7 h-7 rounded-full border border-border-base text-button-default hover:text-button-hover hover:bg-button-hover/10 transition"
                        data-url="{{ route('bouteilles.quantite.update', [$cellierId, $bouteilleId]) }}"
                        data-direction="up"
                        data-bouteille="{{ $bouteilleId }}"
                        data-qty-btn
                        data-cellier-id="{{ $cellierId }}"
                        data-bottle-id="{{ $bouteilleId }}"
                        aria-label="Augmenter la quantité"
                    >
                        +
                    </button>
                </div>
            @endif
        </div>

        @if($isCellierMode)
            {{-- Informations supplémentaires pour le mode cellier --}}
            <div class="text-sm text-text-muted space-y-1">
                @if (!is_null($prix))
                    <p class="text-md">
                        {{ number_format($prix, 2, ',', ' ') }} $
                    </p>
                @endif
            </div>
        @else
            {{-- Prix simple pour le mode catalogue --}}
            <span class='text-text-muted' aria-label="Prix : {{ $prix }} dollars">{{ $prix }} $</span>
        @endif

        {{-- Actions --}}
        @if($isCatalogueMode)
            <div class="flex gap-2 flex-row-reverse flex-wrap justify-end">
                {{-- Formulaire d'ajout au cellier --}}
                <form class="flex gap-3 flex-row-reverse flex-wrap justify-end add-to-cellar-form w-full" aria-label="Ajouter au cellier">
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
                        aria-label="Quantité à ajouter"
                    />

                    <button 
                        type="button"
                        class="flex-3 add-to-cellar-btn bg-button-default active:bg-primary-active hover:bg-button-hover animation duration-200 text-white rounded-lg px-4 py-2"
                        data-bottle-id="{{ $id }}"
                        aria-label="Ajouter {{ $nom }} au cellier"
                    >
                        Ajouter
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>