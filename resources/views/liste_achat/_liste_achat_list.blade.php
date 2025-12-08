<div>
    @php
        // Map des bouteilles catalogue -> bouteille de cellier (si déjà dans un cellier)
        // Ex: $cellarMap[ID_CATALOGUE] = ['cellier_id' => 12, 'bouteille_id' => 34]
        $cellarMap = $cellarMap ?? [];
    @endphp

    {{-- Nombre de résultats --}}
    <p class="mb-2 text-md" role="nombre-resultats">
        <span class="font-semibold">{{ $count }}</span>
        résultat{{ $count > 1 ? 's' : '' }} trouvé{{ $count > 1 ? 's' : '' }}
    </p>

    {{-- La grille de cartes --}}
    <div class="{{ $items->isEmpty() 
            ? 'flex justify-center' 
            : 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4' }} mt-6" role="list">

        @forelse ($items as $item)
            @php
                $b = $item->bouteilleCatalogue;

                // Sécurité : si la bouteille catalogue a été supprimée
                if (!$b) {
                    continue;
                }

                // On regarde si cette bouteille catalogue est déjà présente dans un cellier
                $mapEntry = $cellarMap[$item->bouteille_catalogue_id] ?? null;

                if ($mapEntry) {
                    // Elle existe dans un cellier → lien vers la fiche "cellier"
                    $detailUrl = route('bouteilles.show', [
                        $mapEntry['cellier_id'],
                        $mapEntry['bouteille_id'],
                    ]);
                } else {
                    // Sinon → lien vers la fiche du catalogue
                    $detailUrl = route('catalogue.show', $b->id);
                }
            @endphp

            {{-- Carte EXACTEMENT comme dans index.blade.php, mais image cliquable --}}
            <div id="bouteille-card"
                 class="relative flex flex-col rounded-2xl border border-gray-200 bg-white/80 shadow-sm 
                        hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 overflow-hidden"
                 data-id="{{ $item->id }}">

                {{-- Menu (3 points) --}}
                <x-dropdown-action
                    :id="$item->id"
                    :item="$item"
                    deleteUrl="{{ route('listeAchat.destroy', $item) }}"
                    transferUrl="{{ route('listeAchat.transfer', $item) }}" />

                {{-- Image cliquable (vers fiche produit : cellier OU catalogue) --}}
                <a href="{{ $detailUrl }}" aria-label="Voir les détails de {{ $b->nom }}">
                    <div class="max-h-[160px] w-full bg-gray-200 border-b border-gray-100 flex items-center justify-center 
                                overflow-hidden aspect-3/4 py-3 relative">
                        @if ($b->thumbnail ?? $b->image)
                            {{-- Loading spinner --}}
                            <div class="bottle-image-loader absolute inset-0 flex items-center justify-center bg-gray-200 z-10">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                            </div>
                            <img src="{{ $b->thumbnail ?? $b->image }}"
                                alt="Image {{ $b->nom }}"
                                class="max-w-[96px] max-h-[160px] object-contain bottle-image opacity-0"
                                onload="this.classList.remove('opacity-0'); this.classList.add('opacity-100'); this.parentElement.querySelector('.bottle-image-loader')?.classList.add('hidden')"
                                onerror="this.parentElement.querySelector('.bottle-image-loader')?.classList.add('hidden')">
                        @else
                            <svg  version="1.0" xmlns="http://www.w3.org/2000/svg"  width="90.000000pt" height="90.000000pt" viewBox="0 0 300.000000 300.000000"  preserveAspectRatio="xMidYMid meet">  <g transform="translate(0.000000,300.000000) scale(0.050000,-0.050000)" fill="#757575" stroke="none"> <path d="M2771 5765 c-8 -19 -13 -325 -12 -680 3 -785 6 -767 -189 -955 -231 -222 -214 -70 -225 -2018 -10 -1815 -11 -1791 100 -1831 215 -77 1028 -70 1116 10 73 66 77 168 80 1839 4 1928 18 1815 -254 2058 -141 126 -147 164 -147 878 0 321 -6 618 -13 659 l-12 75 -215 0 c-187 0 -218 -5 -229 -35z"/> </g> </svg> 
                        @endif
                    </div>
                </a>

                {{-- Texte --}}
                <div class="flex-1 p-4 flex flex-col gap-2">

                    {{-- Marquer comme acheté --}}
                    <div class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            class="wishlist-check-achete"
                            data-url="{{ route('listeAchat.update', $item) }}"
                            data-item-id="{{ $item->id }}"
                            @checked($item->achete)
                        >
                        <span class="{{ $item->achete ? 'line-through text-gray-400' : '' }} text-xs font-medium">
                            Acheté
                        </span>
                    </div>

                    <p class="flex-1 font-semibold text-gray-900 text-sm leading-tight line-clamp-2">
                        {{ $b->nom }}
                    </p>

                    {{-- pays + format --}}
                    <p class="text-xs text-gray-500">
                        {{ $b->pays->nom ?? 'Pays inconnu' }} —
                        {{ $b->volume ?? 'Format inconnu' }}
                    </p>

                    {{-- prix / quantité / sous-total --}}
                    <div class="flex flex-col gap-2 text-xs">
                        <p class="text-gray-600">
                            Prix : <span class="font-semibold" data-item-price="{{ $b->prix }}">{{ number_format($b->prix, 2, ',', ' ') }} $</span>
                        </p>

                        <p class="text-gray-700">
                            Sous-total :
                            <span class="font-semibold wishlist-subtotal" data-item-id="{{ $item->id }}">
                                {{ number_format($b->prix * $item->quantite, 2, ',', ' ') }} $
                            </span>
                        </p>

                        {{-- CONTRÔLE QUANTITÉ --}}
                        <div
                            class="flex items-center justify-between bg-neutral-50 border border-border-base rounded-full 
                                   p-0.5 sm:p-1 shadow-sm w-full max-w-[120px] sm:max-w-[150px]">

                            {{-- Bouton - --}}
                            <button
                                type="button"
                                class="wishlist-qty-btn flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full 
                                           text-text-muted hover:text-danger hover:bg-white hover:shadow-sm transition-all duration-200 
                                           active:scale-95"
                                data-direction="down"
                                data-url="{{ route('listeAchat.update', $item) }}"
                                data-item-id="{{ $item->id }}">
                                <x-dynamic-component :component="'lucide-minus'" class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                            </button>

                            {{-- Affichage quantité --}}
                            <div
                                class="wishlist-qty-display text-xs sm:text-sm font-bold text-text-heading text-center select-none px-1"
                                data-item-id="{{ $item->id }}"
                                data-url="{{ route('listeAchat.update', $item) }}">
                                {{ $item->quantite }}
                            </div>

                            {{-- Bouton + --}}
                            <button
                                type="button"
                                class="wishlist-qty-btn flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full 
                                           text-text-muted hover:text-primary hover:bg-white hover:shadow-sm transition-all duration-200 
                                           active:scale-95"
                                data-direction="up"
                                data-url="{{ route('listeAchat.update', $item) }}"
                                data-item-id="{{ $item->id }}">
                                <x-dynamic-component :component="'lucide-plus'" class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            @php
                // Afficher "Aucune bouteille trouvée" seulement si une recherche/filtre est active
                $isSearching = $isSearching ?? false;
            @endphp
            @if($isSearching)
                <x-empty-state 
                    title="Aucune bouteille trouvée"
                    subtitle="Essayez d'ajuster vos filtres ou votre recherche." />
            @endif
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6" role="pagination">
        {{ $items
            ->appends(request()->query())
            ->withPath(route('listeAchat.search'))
            ->links('vendor.pagination.tailwind') }}
    </div>
</div>
