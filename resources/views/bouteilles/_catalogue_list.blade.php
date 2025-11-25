<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mt-6">
    @forelse ($bouteilles as $bouteille)
        <x-bouteille-card-block 
            :id="$bouteille->id" 
            :nom="$bouteille->nom" 
            :image="$bouteille->image" 
            :prix="$bouteille->prix" 
        />
    @empty
        <p class="mt-4 text-center text-gray-500 col-span-full">
            Aucun résultat trouvé.
        </p>
    @endforelse
</div>

<div class="mt-6">
    {{ $bouteilles->links('vendor.pagination.tailwind') }}
</div>
