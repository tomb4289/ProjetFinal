<div>
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
            @endphp

            {{-- Carte EXACTEMENT comme dans index.blade.php --}}
            <div id="bouteille-card" class="relative flex flex-col rounded-2xl border border-gray-200 bg-white/80 shadow-sm 
                        hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 overflow-hidden" data-id="{{ $item->id }}">

                {{-- Menu (3 points) --}}
                <x-dropdown-action
                    :id="$item->id"
                    :item="$item"
                    deleteUrl="{{ route('listeAchat.destroy', $item) }}"
                    transferUrl="{{ route('listeAchat.transfer', $item) }}" />

                {{-- Image --}}
                <div class="max-h-[160px] bg-gray-200 border-b border-gray-100 flex items-center justify-center 
                            overflow-hidden aspect-3/4 py-3">
                    @if ($b->thumbnail ?? $b->image)
                        <img src="{{ $b->thumbnail ?? $b->image }}"
                            alt="Image {{ $b->nom }}"
                            class="max-w-[96px] max-h-[160px] object-contain">
                    @else
                        <x-dynamic-component
                            :component="'lucide-wine'"
                            class="w-7 h-7 text-primary/60" />
                    @endif
                </div>

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
                            Prix : <span class="font-semibold">{{ number_format($b->prix, 2, ',', ' ') }} $</span>
                        </p>

                        <p class="text-gray-700">
                            Sous-total :
                            <span class="font-semibold">
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
            <x-empty-state 
                title="Aucune bouteille trouvée"
                subtitle="Essayez d'ajuster vos filtres ou votre recherche." />
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
