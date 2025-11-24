@props(['id' => '', 'nom' => '', 'image' => null, 'prix' => '', 'userId' => null])
{{-- Carte de bouteille --}}
<a href="{{ route('catalogue.show', $id) }}" class='flex flex-col justify-between bg-card rounded-lg shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden cursor-pointer'>
    
    <picture class='w-full h-32 overflow-hidden bg-neutral-400 flex items-center justify-center'>
        @if ($image === null)
            <p>Aucune Image</p>
        @else
            <img src='{{ $image }}' alt='Image de la bouteille {{ $nom }}' class='w-full h-full object-cover hover:scale-105 transition-transform duration-300'/>
        @endif
    </picture>
    <div class='p-4 flex flex-col gap-2'>
        <span class='truncate font-semibold text-text-title text-md'>{{ $nom }}</span>
        <span class='text-text-muted'>{{ $prix }} $</span>
        <form class="flex gap-3 flex-row-reverse flex-wrap justify-end add-to-cellar-form stop-link-propagation">
            <input 
                type="hidden" 
                name="bottle_id" 
                value="{{ $id }}"
            >

            <input 
                type="number"
                name="quantity"
                min="1"
                max="10"
                value="1"
                class="flex-1 text-center border border-gray-300 rounded-lg px-2 py-1"
            />

            <button 
                type="submit"
                class="flex-3 add-to-cellar-btn bg-primary active:bg-primary-active hover:bg-primary-hover animation duration-200 text-white rounded-lg px-4 py-2"
                data-bottle-id="{{ $id }}"
            >
                Ajouter
            </button>
</form>
    </div>
</a>