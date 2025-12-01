<div
    id="addWineOverlay"
    class="fixed inset-0 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300 z-40"
    aria-hidden="true"
></div>
<div
    id="addWineBtnContainer"
    class="fixed z-50 bottom-0 left-0 w-full p-4 py-10 bg-card border border-border-base shadow-lg rounded-t-lg transform translate-y-full transition-transform duration-300"
    role="dialog"
    aria-modal="true"
    aria-labelledby="titre-ajout-vin-modal"
>
    <span class="flex items-center justify-between mb-4">
        {{-- Ajout d'un ID pour lier le titre à la modale --}}
        <h1 id="titre-ajout-vin-modal" class="text-3xl text-heading font-heading">Ajouter un vins</h1>

        <x-dynamic-component
            :component="'lucide-x'"
            id="closeAddWine"
            class="w-6 h-6 cursor-pointer"
        />
    </span>

    {{-- Where all cellars will be injected --}}
    <div 
        id="cellar-list" 
        class="space-y-2 flex flex-col g-4 overflow-auto max-h-60"
        role="list"
        aria-label="Liste des options d'ajout"
    ></div>
</div>