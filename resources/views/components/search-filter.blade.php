@props([
    'pays' => [],
    'types' => [],
])

{{-- Composant de recherche et filtres --}}
<div {{ $attributes->merge(['class' => 'mt-4']) }}>
    <x-input 
        type="text" 
        name="searchInput"
        placeholder="Rechercher..." 
    />
    <div class="flex gap-3 my-4">
        <select id="paysFilter" class="border px-3 py-2 rounded-lg">
            <option value="">Toutes les Pays</option>
            @foreach($pays as $p)
                <option value="{{ $p->id }}">{{ $p->nom }}</option>
            @endforeach
        </select>

        <select id="typeFilter" class="border px-3 py-2 rounded-lg">
            <option value="">Tous les Types</option>
            @foreach($types as $t)
                <option value="{{ $t->id }}">{{ $t->nom }}</option>
            @endforeach
        </select>
    </div>
</div>