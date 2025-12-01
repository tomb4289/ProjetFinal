@props([  
    'pays' => [],
    'types' => [],
    'millesimes' => [],
    // 'catalogue' par défaut, 'cellier' pour ton écran de cellier
    'mode' => 'catalogue',
])

{{-- Composant de recherche et filtres (réutilisable cellier + catalogue) --}}
<div {{ $attributes->merge(['class' => 'mt-4 mb-4']) }} role="search" aria-label="Barre de recherche et filtres">
    <div class="relative flex gap-5 items-center"> 
        <x-input 
            type="text" 
            name="searchInput"
            id="searchInput"
            placeholder="Rechercher..."
        />
        
        {{-- Bouton pour ouvrir le panneau de filtres / tri --}}
        <button 
            id="sortOptionsBtn" 
            type="button"
            class="p-2 bg-card border border-muted rounded-lg hover:bg-gray-100"
            aria-label="Ouvrir les options de tri et de filtre"
            aria-haspopup="dialog"
            aria-expanded="false"
        >
            <x-dynamic-component :component="'lucide-sliders-horizontal'" class="w-6 stroke-text-heading" />
        </button>

        {{-- Boîte de suggestions de recherche --}}
        <div
            id="suggestionsBox"
            class="absolute left-0 top-full bg-white border border-border-base rounded-lg shadow-md hidden z-50 max-h-50 overflow-y-auto"
            role="listbox"
            aria-label="Suggestions de recherche"
        ></div>
    </div>

    {{-- Overlay sombre derrière le panneau --}}
    <div
        id="filtersOverlay"
        class="fixed inset-0 bg-black z-40 hidden transition-opacity duration-300 ease-in-out opacity-0"
        aria-hidden="true"
    ></div>

    {{-- Panneau de filtres / tri (bottom sheet) --}}
    <div 
        id="filtersContainer" 
        class="rounded-t-3xl fixed bottom-0 left-0 w-full h-[75vh] bg-card p-6 shadow-2xl z-50
               transform translate-y-[100%] transition-transform duration-500 ease-in-out hidden overflow-y-auto"
        role="dialog"
        aria-modal="true"
        aria-labelledby="filter-title"
    >
        <div id="dragHandle" class="w-20 h-2 bg-gray-200 rounded-full mx-auto mb-4" aria-hidden="true"></div>

        <div class="flex justify-between items-center my-6">
            <h3 id="filter-title" class="text-xl font-bold">Options de filtre</h3>
        </div>

        {{-- Contenu des filtres --}}
        <div class="flex gap-3 my-4 flex-wrap flex-col sm:flex-row">
            {{-- Select pour trier --}}
            <select id="sortFilter" class="border px-5 py-3 rounded-lg flex-1" aria-label="Trier par">
                <option value="date_import-desc" selected>Trier par...</option>
                <option value="prix-asc">Prix (le moins cher)</option>
                <option value="prix-desc">Prix (le plus cher)</option>
                <option value="nom-asc">Nom (A - Z)</option>
                <option value="nom-desc">Nom (Z - A)</option>
                <option value="millesime-desc">Millésime (le plus récent)</option>
                <option value="millesime-asc">Millésime (le plus ancien)</option>
            </select>

            {{-- Select pour Pays --}}
            <select id="paysFilter" class="border px-5 py-3 rounded-lg" aria-label="Filtrer par pays">
                <option value="">Tous les pays</option>
                @foreach($pays as $p)
                    @php
                        // catalogue = id_pays / cellier = texte du pays
                        $paysValue = $mode === 'cellier' ? $p->nom : $p->id;
                    @endphp
                    <option value="{{ $paysValue }}">{{ $p->nom }}</option>
                @endforeach
            </select>

            {{-- Select pour Millésime --}}
            <select id="millesimeFilter" class="border px-5 py-3 rounded-lg" aria-label="Filtrer par millésime">
                <option value="">Tous les millésimes</option>
                @foreach($millesimes as $m)
                    <option value="{{ $m->millesime }}">{{ $m->millesime }}</option>
                @endforeach
            </select>

            {{-- Select pour Type --}}
            <select id="typeFilter" class="border px-5 py-3 rounded-lg" aria-label="Filtrer par type de vin">
                <option value="">Tous les types</option>
                @foreach($types as $t)
                    @php
                        // catalogue = id_type_vin / cellier = texte du type
                        $typeValue = $mode === 'cellier' ? $t->nom : $t->id;
                    @endphp
                    <option value="{{ $typeValue }}">{{ $t->nom }}</option>
                @endforeach
            </select>

            {{-- Inputs pour Prix min / max --}}
            <div class="flex w-full" role="group" aria-label="Plage de prix">
                <x-input 
                    type="number" 
                    name="priceMin"
                    id="priceMin"
                    placeholder="Prix min" 
                    min="0"
                    size="full"
                    class="w-24 px-5 py-3 flex-1"
                />
                <span class="mx-2 self-center" aria-hidden="true">-</span>
                <x-input 
                    type="number" 
                    name="priceMax"
                    id="priceMax"
                    size="full"
                    placeholder="Prix max" 
                    min="0"
                    class="w-24 px-5 py-3 flex-1"
                />
            </div>

            {{-- Boutons en bas : Réinitialiser + Appliquer --}}
            <div class="flex gap-3 mt-4 flex-1 sm:flex-0">
                <button
                    id="resetFiltersBtn"
                    type="button"
                    class="flex-1 px-4 py-2 bg-card border border-danger text-danger rounded-lg hover:bg-danger-hover hover:text-white transition"
                    aria-label="Réinitialiser tous les filtres"
                >
                    Réinitialiser
                </button>

                <button
                    id="applyFiltersBtn"
                    type="button"
                    class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition"
                    aria-label="Appliquer les filtres sélectionnés"
                >
                    Filtrer
                </button>
            </div>
        </div>
    </div>
</div>