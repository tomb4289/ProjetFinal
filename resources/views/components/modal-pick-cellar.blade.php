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

{{-- HTML Templates for dynamic content --}}
<template id="loading-template">
    <div class="py-4 text-center text-gray-400 animate-pulse">
        <div class="flex items-center justify-center gap-2">
            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary"></div>
            <span>Chargement...</span>
        </div>
    </div>
</template>

<template id="empty-cellars-template">
    <div class="flex flex-col items-center gap-4">
        <p class="text-gray-400 italic">
            Vous n'avez pas encore de cellier. Veuillez en créer un d'abord.
        </p>
        <a href="/celliers/create" class="bg-button-default border-2 border-primary text-primary hover:text-white px-4 py-2 rounded-lg w-40 text-center hover:bg-button-hover transition">Créer un cellier</a>
    </div>
</template>

<template id="cellar-item-template">
    <a 
        href="#"
        class="cellar-box block p-3 bg-card rounded-lg shadow-md border border-border-base hover:shadow-sm cursor-pointer"
        data-cellar-id=""
        data-bottle-id=""
        data-quantity=""
    >
        <div class="flex justify-between">
            <div class="flex flex-col gap-1">
                <h2 class="text-2xl font-semibold cellar-name"></h2>
                <p class="cellar-count"></p>
            </div>
        </div>
    </a>
</template>