{{-- Props permis --}}
@props(['name'=>'Cellier', 'amount' => '0', 'id' => '', 'editable' => true])

<div 
    class="cellar-box relative p-3 bg-card rounded-lg shadow-md border border-border-base hover:shadow-sm transition-all duration-300"
    role="article" 
    aria-labelledby="cellier-title-{{ $id }}"
>
    {{-- Lien cliquable partout --}}
    <a 
        href="/celliers/{{ $id }}" 
        class="absolute inset-0 z-0" 
        aria-label="Ouvrir le cellier {{ $name }}"
    ></a>

    {{-- Contenu : pointer-events-none pour laisser le lien cliquer partout --}}
    <div class="relative z-10 flex justify-between pointer-events-none">
        <div class="flex flex-col gap-1">
            <h2 id="cellier-title-{{ $id }}" class="text-2xl font-semibold">{{ $name }}</h2>
            @if ($amount == 0)
                <p class="text-gray-400 italic">Aucune bouteille</p>
            @elseif ($amount == 1)
                <p class="text-gray-600">1 Bouteille unique</p>
            @else
                <p class="text-gray-600">{{ $amount }} Bouteilles uniques</p>
            @endif
        </div>

        @if ($editable === true)
                {{-- Boutons : pointer-events-auto pour réactiver les clics --}}
            <div 
                class="cellar-action-btns hidden flex gap-2 items-center pointer-events-auto"
                role="group" 
                aria-label="Actions pour le cellier {{ $name }}"
            >
                <x-edit-btn :route="route('cellar.edit', $id)" />
                <x-delete-btn :route="route('cellar.destroy', $id)" />
            </div>    
        @endif
        
    </div>
</div>