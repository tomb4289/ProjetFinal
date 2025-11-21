@props(['nom' => '', 'image' => null, 'prix' => ''])
{{-- Carte de bouteille --}}
<div class='flex flex-col bg-card rounded-lg shadow-md hover:shadow-sm transition-all duration-300 overflow-hidden'>
    
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
    </div>
</div>