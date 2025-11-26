@props([
    'pays' => [],
    'types' => [],
    'millesimes' => [],
])

{{-- Composant de recherche et filtres --}}
<div {{ $attributes->merge(['class' => 'mt-4']) }}>
    <div class="relative w-full">
    <x-input 
        type="text" 
        name="searchInput"
        placeholder="Rechercher..." 
    />
    {{-- Boite de suggestions de recherche --}}
    <div id="suggestionsBox" class="absolute left-0 right-0 bg-white border border-border-base rounded-lg shadow-md hidden z-50 max-h-50 overflow-y-auto">
    </div>
    </div>
    
    <div class="flex gap-3 my-4 flex-wrap">
        <select id="paysFilter" class="border px-3 py-2 rounded-lg">
            <option value="">Toutes les Pays</option>
            @foreach($pays as $p)
                <option value="{{ $p->id }}">{{ $p->nom }}</option>
            @endforeach
        </select>

        <select id="millesimeFilter" class="border px-3 py-2 rounded-lg">
            <option value="">Tous les Millésimes</option>
            @foreach($millesimes as $m)
                <option value="{{ $m->millesime }}">{{ $m->millesime }}</option>
            @endforeach
        </select>

        <select id="typeFilter" class="border px-3 py-2 rounded-lg">
            <option value="">Tous les Types</option>
            @foreach($types as $t)
                <option value="{{ $t->id }}">{{ $t->nom }}</option>
            @endforeach
        </select>

        <div>
            <x-input 
                type="number" 
                name="priceMin"
                placeholder="Prix min" 
                class="w-24"
            />
            <x-input 
                type="number" 
                name="priceMax"
                placeholder="Prix max" 
                class="w-24 mt-2"
            />
        </div>
    </div>
</div>