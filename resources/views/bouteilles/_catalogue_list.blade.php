<div>
<p class="mb-2 text-md" role="nombre-resultats">
    <span class="font-semibold">{{ $count }}</span> résultat{{ $count > 1 ? 's' : '' }} trouvé{{ $count > 1 ? 's' : '' }}
</p>   
 {{-- La grille --}}
    <div class="
    {{ $bouteilles->isEmpty() 
            ? 'flex justify-center' 
            : 'grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5' }} gap-4 mt-6" role="list">
        @forelse ($bouteilles as $bouteille)
            <x-bouteille-card-block 
                :id="$bouteille->id" 
                :nom="$bouteille->nom" 
                :image="$bouteille->image" 
                :prix="$bouteille->prix" 
            />
        @empty
            <x-empty-state 
                title="Aucune bouteille trouvée" 
                subtitle="Essayez d'ajuster vos filtres ou votre recherche pour trouver des bouteilles."
            />
        @endforelse
    </div>

    {{-- La pagination mise à jour --}}
    <div class="mt-6" role="pagination">
        {{ $bouteilles
            ->appends(request()->query())
            ->withPath(route('catalogue.search'))
            ->links('vendor.pagination.tailwind') }}
    </div>
</div>
