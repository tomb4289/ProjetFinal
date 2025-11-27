@props([
    'pays' => [],
    'types' => [],
    'millesimes' => [],
])

{{-- Composant de recherche et filtres --}}
<div {{ $attributes->merge(['class' => 'mt-4 mb-4']) }}>
    <div class="relative flex gap-5 items-center "> 
        <x-input 
            type="text" 
            name="searchInput"
            id="searchInput"
            placeholder="Rechercher..."
        />
        <button 
            id="sortOptionsBtn" 
            class="p-2 bg-card border border-muted rounded-lg hover:bg-gray-100"
        ><x-dynamic-component :component="'lucide-sliders-horizontal'" class="w-6 stroke-text-heading "/></button>
        {{-- Boite de suggestions de recherche --}}
        <div id="suggestionsBox" class="absolute left-0 top-full bg-white border border-border-base rounded-lg shadow-md hidden z-50 max-h-50 overflow-y-auto">
        </div>
    </div>

    <div id="filtersOverlay" class="fixed inset-0 bg-black z-40 hidden transition-opacity duration-300 ease-in-out opacity-0"></div>

   
    <div 
        id="filtersContainer" 
        class="rounded-t-3xl fixed bottom-0 left-0 w-full  h-[75vh] bg-card p-6 shadow-2xl z-50 transform translate-y-[100%] transition-transform duration-500 ease-in-out hidden overflow-y-auto"
    >
        <div id="dragHandle" class="w-20 h-2 bg-gray-200 rounded-full mx-auto mb-4"></div>
        <div class="flex justify-between items-center my-6">
            <h3 class="text-xl font-bold">Options de Filtre</h3>
        </div>

        {{-- Contenu des filtres --}}
        <div class="flex gap-3 my-4 flex-wrap flex-col sm:flex-row">
            {{-- Select pour trier --}}
            <select id="sortFilter" class="border px-5 py-3 rounded-lg flex-1">
                <option value="">Trier par...</option>
                <option value="prix-asc">Prix (le moins cher)</option>
                <option value="prix-desc">Prix (le plus cher)</option>
                <option value="nom-asc">Nom (A - Z)</option>
                <option value="nom-desc">Nom (Z - A)</option>
                <option value="millesime-desc">Millésime (le plus récent)</option>
                <option value="millesime-asc">Millésime (le plus ancien)</option>
            </select>

            {{-- Sekect pour Pays --}}
            <select id="paysFilter" class="border px-5 py-3 rounded-lg">
                <option value="">Toutes les Pays</option>
                @foreach($pays as $p)
                    <option value="{{ $p->id }}">{{ $p->nom }}</option>
                @endforeach
            </select>

            {{-- Select pour Millésime --}}
            <select id="millesimeFilter" class="border px-5 py-3 rounded-lg">
                <option value="">Tous les Millésimes</option>
                @foreach($millesimes as $m)
                    <option value="{{ $m->millesime }}">{{ $m->millesime }}</option>
                @endforeach
            </select>

            {{-- Select pour Type --}}
            <select id="typeFilter" class="border px-5 py-3 rounded-lg">
                <option value="">Tous les Types</option>
                @foreach($types as $t)
                    <option value="{{ $t->id }}">{{ $t->nom }}</option>
                @endforeach
            </select>

            {{-- Inputs pour Prix --}}
            <div class="flex w-full ">
                <x-input 
                    type="number" 
                    name="priceMin"
                    id="priceMin"
                    placeholder="Prix min" 
                    min="0"
                    size='full'
                    class="w-24 px-5 py-3 flex-1"
                />
                <span class="mx-2 self-center">-</span>
                <x-input 
                    type="number" 
                    name="priceMax"
                    id="priceMax"
                    size='full'
                    placeholder="Prix max" 
                    min="0"
                    class="w-24 px-5 py-3"
                />
            </div>
            {{-- Reset des filtres --}}
            <button id="resetFiltersBtn" class="p-2 bg-card border text-danger border-danger rounded-lg hover:bg-danger-hover">Réinitialiser</button>
        </div>
    </div>
</div>