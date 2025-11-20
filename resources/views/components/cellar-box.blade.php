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
