{{-- Liste des bouteilles d'un cellier – utilisée par la page et par l'AJAX --}}

@php
    // On accepte soit $bouteilles passé par le contrôleur (search),
    // soit la relation $cellier->bouteilles (show classique)
    $bottles = isset($bouteilles) ? $bouteilles : ($cellier->bouteilles ?? collect());

    // Est-ce qu'il y a au moins un filtre actif dans la requête ?
    $hasFilters = request()->filled('nom')
        || request()->filled('pays')
        || request()->filled('type')
        || request()->filled('millesime')
        || request()->filled('prix_min')
        || request()->filled('prix_max')
        || request()->filled('region');

    // Nombre total de bouteilles dans le cellier (sans filtres)
    $totalBottlesCount = $totalBottlesCount ?? ($cellier->bouteilles()->count() ?? 0);
    $isCellierEmpty = $totalBottlesCount === 0;
@endphp

<div aria-label="Contenu du cellier">
    @if ($bottles->isEmpty())
        {{-- Toujours afficher "Ce cellier est vide" si le cellier est vide --}}
        @if ($isCellierEmpty)
            <x-empty-state 
                title="Ce cellier est vide" 
                subtitle="Ajoutez des bouteilles à ce cellier pour les voir apparaître ici."
                actionLabel="Explorer le catalogue"
                actionUrl="{{ route('bouteille.catalogue') }}"
            />
        @endif

        {{-- Afficher "Aucune bouteille ne correspond aux filtres" si des filtres sont actifs --}}
        @if ($hasFilters)
            <x-empty-state 
                title="Aucune bouteille ne correspond aux filtres appliqués" 
                subtitle="Essayez d'ajuster ou de supprimer certains filtres pour voir plus de bouteilles dans ce cellier."
            />
        @endif
    @else
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4" aria-label="Liste des bouteilles">
            @foreach ($bottles as $bouteille)
                <x-bouteille-card-block 
                    :id="$bouteille->id" 
                    :nom="$bouteille->nom" 
                    :image="$bouteille->getThumbnailFromCatalogue() ?? $bouteille->getImageFromCatalogue()"
                    :prix="$bouteille->prix ?? ''" 
                    mode="cellier"
                    :cellierId="$cellier->id"
                    :bouteilleId="$bouteille->id"
                    :quantite="$bouteille->quantite ?? 1"
                    :pays="$bouteille->pays ?? null"
                    :format="$bouteille->format ?? null"
                    :codeSaq="$bouteille->code_saq ?? null"
                />
            @endforeach
        </div>
    @endif
</div>