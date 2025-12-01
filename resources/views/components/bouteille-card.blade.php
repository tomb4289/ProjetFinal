@props([
    'nom' => '',
    'type' => null,
    'millesime' => null,
    'quantite' => null,
    'urlImage' => null,
    'pays' => null,
    'region' => null,
    'volume' => null,
    'prix' => null,
    'codeSaq' => null,
    'url' => null,
    'id' => null
])

{{-- Carte de bouteille avec tous les détails --}}
@if($id)
    <a 
        href="{{ route('catalogue.show', $id) }}" 
        class="bg-card border border-border-base rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 flex max-w-[500px] cursor-pointer"
        aria-label="Voir les détails de {{ $nom }}"
    >
@else
    <div 
        class="bg-card border border-border-base rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 flex max-w-[500px]"
        role="article"
        aria-label="Carte de la bouteille {{ $nom }}"
    >
@endif
    {{-- Image de la bouteille à gauche --}}
    <div class="relative w-32 sm:w-40 flex-shrink-0 bg-gray-50 flex items-center justify-center overflow-hidden">
        @if($urlImage)
            @php
                // Normaliser le chemin : enlever tous les préfixes storage/ et / au début
                $imagePath = ltrim($urlImage, '/');
                // Enlever tous les préfixes "storage/" jusqu'à ce qu'il n'y en ait plus
                while (str_starts_with($imagePath, 'storage/')) {
                    $imagePath = substr($imagePath, 8); // Enlever "storage/" (8 caractères)
                }
                // Ajouter storage/ une seule fois à la fin
                $imagePath = 'storage/' . $imagePath;
            @endphp
            <img src="{{ asset($imagePath) }}" 
                 alt="Bouteille {{ $nom }}" 
                 class="w-full h-full object-contain p-3">
        @else
            <div class="flex items-center justify-center p-3">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-label="Aucune image disponible">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        @endif
    </div>

    {{-- Informations au centre --}}
    <div class="flex-1 p-4 space-y-2 min-w-0">
        {{-- Nom de la bouteille --}}
        <h3 class="font-semibold text-lg text-text-heading line-clamp-2">
            {{ $nom }}
        </h3>

        {{-- Informations principales --}}
        <div class="space-y-1.5" role="list" aria-label="Caractéristiques">
            {{-- Type de vin --}}
            @if($type)
                <div class="flex items-center gap-2" role="listitem">
                    <span class="text-xs font-medium text-muted uppercase tracking-wide">Type</span>
                    <span class="text-sm text-text-body">{{ $type }}</span>
                </div>
            @endif

            {{-- Millésime --}}
            @if($millesime)
                <div class="flex items-center gap-2" role="listitem">
                    <span class="text-xs font-medium text-muted uppercase tracking-wide">Millésime</span>
                    <span class="text-sm text-text-body font-medium">{{ $millesime }}</span>
                </div>
            @endif

            {{-- Pays --}}
            @if($pays)
                <div class="flex items-center gap-2" role="listitem">
                    <span class="text-xs font-medium text-muted uppercase tracking-wide">Pays</span>
                    <span class="text-sm text-text-body">{{ $pays }}</span>
                </div>
            @endif

            {{-- Région --}}
            @if($region)
                <div class="flex items-center gap-2" role="listitem">
                    <span class="text-xs font-medium text-muted uppercase tracking-wide">Région</span>
                    <span class="text-sm text-text-body">{{ $region }}</span>
                </div>
            @endif

            {{-- Volume --}}
            @if($volume)
                <div class="flex items-center gap-2" role="listitem">
                    <span class="text-xs font-medium text-muted uppercase tracking-wide">Volume</span>
                    <span class="text-sm text-text-body">{{ $volume }}</span>
                </div>
            @endif
        </div>

        {{-- Prix et code SAQ --}}
        <div class="pt-2 border-t border-border-base flex items-center justify-between flex-wrap gap-2">
            @if($prix !== null)
                <span class="text-lg font-bold text-primary" aria-label="Prix : {{ number_format($prix, 2) }} dollars">
                    {{ number_format($prix, 2) }} $
                </span>
            @endif
            @if($codeSaq)
                <span class="text-xs text-muted" aria-label="Code SAQ : {{ $codeSaq }}">Code SAQ: {{ $codeSaq }}</span>
            @endif
        </div>
    </div>
@if($id)
    </a>
@else
</div>
@endif