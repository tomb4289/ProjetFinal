@props([
    'name'   => 'Cellier',
    'amount' => 0,
    'id'     => null,
])

<a
    class="cellar-box p-3 bg-card rounded-lg flex justify-between shadow-md border-border-base border hover:shadow-sm hover:bg-card-hover transition-all duration-300"
    href="{{ $id ? route('cellar.show', $id) : '#' }}"
>
    <div class="flex flex-col gap-1">
        <h2 class="text-2xl font-semibold">{{ $name }}</h2>

        @if ($amount == 0)
            <p class="text-gray-400 italic">Aucune bouteille</p>
        @elseif ($amount == 1)
            <p class="text-gray-600">1 Bouteille</p>
        @else
            <p class="text-gray-600">{{ $amount }} Bouteilles</p>
        @endif
    </div>

    <div class="cellar-action-btns flex gap-2 justify-center items-center hidden">
        <x-edit-btn   :id="$id" :route="route('cellar.edit', $id)" />
        <x-delete-btn :id="$id" :route="route('cellar.destroy', $id)" />
    </div>
</a>
@props(['name'=>'Cellier', 'amount' => '0', 'id' => ''])

<div class="cellar-box relative p-3 bg-card rounded-lg shadow-md border border-border-base hover:shadow-sm transition-all duration-300">

    {{-- Lien cliquable partout --}}
    <a href="/celliers/{{ $id }}" class="absolute inset-0 z-0"></a>

    {{-- Contenu : pointer-events-none pour laisser le lien cliquer partout --}}
    <div class="relative z-10 flex justify-between pointer-events-none">

        <div class="flex flex-col gap-1">
            <h2 class="text-2xl font-semibold">{{ $name }}</h2>

            @if ($amount == 0)
                <p class="text-gray-400 italic">Aucune bouteille</p>
            @elseif ($amount == 1)
                <p class="text-gray-600">1 Bouteille</p>
            @else
                <p class="text-gray-600">{{ $amount }} Bouteilles</p>
            @endif
        </div>

        {{-- Boutons : pointer-events-auto pour réactiver les clics --}}
        <div class="cellar-action-btns hidden flex gap-2 items-center pointer-events-auto">
            <x-edit-btn :route="route('cellar.edit', $id)" />
            <x-delete-btn :route="route('cellar.destroy', $id)" />
        </div>

    </div>
</div>
